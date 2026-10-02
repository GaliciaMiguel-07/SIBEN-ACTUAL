<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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

class TwoFactorController extends Controller
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
            session()->forget('2fa_pending_user');
            return redirect()->route('login');
        }

        // window=2 → tolera ±1 minuto (reloj del teléfono ligeramente desfasado)
        $valido = $this->google2fa()->verifyKey($usuario->google2fa_secret, $request->codigo, 2);

        if (!$valido) {
            return back()->withErrors(['codigo' => 'El código es incorrecto o ya expiró.']);
        }

        Auth::login($usuario);
        $request->session()->regenerate();
        session()->forget('2fa_pending_user');

        // Limpiar bandera de suplantación huérfana si existiera
        $usuario->impersonating_user_id = null;
        $usuario->save();

        return redirect()->route('dashboard');
    }

    // ─── Configuración (usuario YA autenticado) ──────────────────────────────

    public function showSetup(): View
    {
        $usuario  = auth()->user();
        $google2fa = $this->google2fa();

        // Si ya tiene 2FA, muestra el mismo secreto (para re-escaneo);
        // si no, genera uno nuevo (aún no se guarda: se guarda tras confirmar el código)
        $secret = $usuario->google2fa_secret ?? $google2fa->generateSecretKey();

        $qrUrl = $google2fa->getQRCodeUrl('SIBEN', $usuario->id_usuario, $secret);

        $renderer = new ImageRenderer(new RendererStyle(220), new SvgImageBackEnd());
        $writer   = new Writer($renderer);
        $qrSvg    = $writer->writeString($qrUrl);

        return view('2fa.configurar', [
            'secret' => $secret,
            'qrSvg'  => $qrSvg,
        ]);
    }

    public function enable(Request $request): RedirectResponse
    {
        $request->validate([
            'codigo' => 'required|string|size:6',
        ]);

        $usuario = auth()->user();
        $secret  = $request->input('secret');

        if (!$this->google2fa()->verifyKey($secret, $request->codigo, 2)) {
            return back()->withErrors([
                'codigo' => 'El código no coincide. Revisa que el reloj de tu teléfono esté sincronizado.',
            ]);
        }

        $usuario->google2fa_secret = $secret;
        $usuario->save();

        return redirect()->route('dashboard')->with('ok', '2FA activado correctamente.');
    }

    public function disable(Request $request): RedirectResponse
    {
        $usuario = auth()->user();
        $usuario->google2fa_secret = null;
        $usuario->save();

        return redirect()->route('dashboard')->with('ok', '2FA desactivado.');
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