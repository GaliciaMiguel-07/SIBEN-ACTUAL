<?php

namespace App\Http\Controllers;

/**
 * El dashboard no duplica la lógica de inicio.
 * Toda la preparación de contexto, menú, mensajes y avisos vive en sibenController.
 */
class DashboardController extends sibenController
{
    public function index()
    {
        return parent::index();
    }
}
