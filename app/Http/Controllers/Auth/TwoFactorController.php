<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\sibenController;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorController extends sibenController
{
    private function google2fa(): Google2FA
    {
        return new Google2FA();
    }

    // ─── Verificación durante el login (usuario aún NO autenticado) ──────────

    public function showVerifyForm(): View|RedirectResponse
    {
        if (!session('2fa_pending_user')) {
            return redirect()->route('login');
        }
        return view('2fa.verificar');
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'codigo' => 'required|string|size:6',
        ], [
            'codigo.required' => 'Ingresa el código de 6 dígitos.',
            'codigo.size'     => 'El código debe tener 6 dígitos.',
        ]);

        $userId = session('2fa_pending_user');
        if (!$userId) {
            return redirect()->route('login');
        }

        $usuario = Usuario::find($userId);
        if (!$usuario || !$usuario->google2fa_secret) {
            session()->forget(['2fa_pending_user', '2fa_setup_required', '2fa_remember']);
            return redirect()->route('login');
        }

        // window=1 → tolera ±30 segundos
        $valido = $this->google2fa()->verifyKey($usuario->google2fa_secret, $request->codigo, 1);

        if (!$valido) {
            return back()->withErrors(['codigo' => 'El código es incorrecto o ya expiró.']);
        }

        Auth::login($usuario, (bool) session('2fa_remember', false));
        $request->session()->regenerate();
        session()->forget(['2fa_pending_user', '2fa_setup_required', '2fa_remember']);

        if ($usuario->impersonating_user_id) {
            $usuario->impersonating_user_id = null;
            $usuario->save();
        }

        return redirect()->route('dashboard');
    }

    // ─── Configuración (usuario pendiente o autenticado) ─────────────────────

    public function showSetup(): View|RedirectResponse
    {
        $usuario = auth()->user();

        if (!$usuario) {
            $pendingUserId = session('2fa_pending_user');
            if (!$pendingUserId) {
                return redirect()->route('login');
            }

            $usuario = Usuario::find($pendingUserId);
            if (!$usuario) {
                session()->forget(['2fa_pending_user', '2fa_setup_required', '2fa_remember']);
                return redirect()->route('login');
            }
        }

        $google2fa = $this->google2fa();

        // Si ya tiene 2FA, muestra el mismo secreto (para re-escaneo);
        // si no, genera uno nuevo (aún no se guarda: se guarda tras confirmar el código)
        $secret = $usuario->google2fa_secret ?? $google2fa->generateSecretKey(16);

        $qrUrl = $google2fa->getQRCodeUrl('SIBEN', $usuario->id_usuario, $secret);

        $renderer = new ImageRenderer(new RendererStyle(220), new SvgImageBackEnd());
        $writer   = new Writer($renderer);
        $qrSvg    = $writer->writeString($qrUrl);

        return view('2fa.configurar', [
            'secret' => $secret,
            'qrSvg'  => $qrSvg,
            'isPendingSetup' => (bool) session('2fa_pending_user') && !Auth::check(),
        ]);
    }

    public function enable(Request $request): RedirectResponse
    {
        $request->validate([
            'codigo' => 'required|string|size:6',
            'secret' => 'required|string',
        ]);

        $pendingUserId = session('2fa_pending_user');
        $isPendingSetup = !$request->user() && !empty($pendingUserId);

        if ($isPendingSetup) {
            $usuario = Usuario::find($pendingUserId);
            if (!$usuario) {
                session()->forget(['2fa_pending_user', '2fa_setup_required', '2fa_remember']);
                return redirect()->route('login');
            }
        } else {
            $usuario = $request->user();
            if (!$usuario) {
                return redirect()->route('login');
            }
        }

        $secret  = $request->input('secret');

        if (!$this->google2fa()->verifyKey($secret, $request->codigo, 1)) {
            return back()->withErrors([
                'codigo' => 'El código no coincide. Revisa que el reloj de tu teléfono esté sincronizado.',
            ])->withInput($request->except('codigo'));
        }

        $usuario->google2fa_secret = $secret;
        $usuario->save();

        if ($isPendingSetup) {
            Auth::login($usuario, (bool) session('2fa_remember', false));
            $request->session()->regenerate();
            session()->forget(['2fa_pending_user', '2fa_setup_required', '2fa_remember']);

            if ($usuario->impersonating_user_id) {
                $usuario->impersonating_user_id = null;
                $usuario->save();
            }

            return redirect()->route('dashboard')->with('ok', '2FA activado correctamente.');
        }

        return redirect()->route('dashboard')->with('ok', '2FA activado correctamente.');
    }

    public function disable(Request $request): RedirectResponse
    {
        return redirect()->route('dashboard')
            ->with('error', 'No se puede desactivar el 2FA: es obligatorio para todos los usuarios.');
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // IMPERSONACIÓN: Admin entra como otro usuario sin 2FA
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Impersonate a user (admin only).
     *
     * El admin accede a la cuenta de otro usuario sin pasar por 2FA.
     * El usuario original mantiene su secreto TOTP intacto.
     *
     * Route: POST /admin/impersonate/{usuarioId}
     */
    public function impersonate(Request $request, $usuarioId)
    {
        // Solo admins (nivel 1) pueden suplantar
        if (auth()->user()->nivel !== 1) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permisos para esta acción.');
        }

        // Buscar el usuario a suplantar
        $targetUser = Usuario::where('id', $usuarioId)->first();
        if (!$targetUser) {
            return redirect()->route('admin.usuarios.index')
                ->with('error', 'Usuario no encontrado.');
        }

        // No se puede suplantar la propia cuenta
        if ($targetUser->id === auth()->id()) {
            return redirect()->route('admin.usuarios.index')
                ->with('error', 'No puede entrar como su propia cuenta.');
        }

        // Guardar el ID del admin original en el perfil del usuario suplantado
        $targetUser->impersonating_user_id = auth()->id();
        $targetUser->save();

        // Hacer logout del admin actual y login como el usuario objetivo
        auth()->logout();
        auth()->loginUsingId($targetUser->id);

        // Redirigir al dashboard con aviso
        return redirect()->route('dashboard')
            ->with('ok', "Ahora estás operando como {$targetUser->id_usuario}.");
    }

    /**
     * Stop impersonating and return to admin account.
     *
     * Route: POST /admin/stop-impersonating
     */
    public function stopImpersonating()
    {
        // Verificar que hay una suplantación activa
        $currentUser = auth()->user();
        if (!$currentUser->impersonating_user_id) {
            return redirect()->route('dashboard')
                ->with('error', 'No estás suplantando a nadie.');
        }

        // Guardar el ID del admin original antes de hacer logout
        $adminId = $currentUser->impersonating_user_id;

        // Limpiar la bandera de suplantación en el usuario objetivo
        $currentUser->impersonating_user_id = NULL;
        $currentUser->save();

        // Logout del usuario suplantado y login del admin original
        auth()->logout();
        auth()->loginUsingId($adminId);

        // Redirigir a la lista de usuarios
        return redirect()->route('admin.usuarios.index')
            ->with('ok', 'Volviste a tu cuenta de administrador.');
    }
}