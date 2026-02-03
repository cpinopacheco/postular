<?php

namespace App\Controllers;

/* Maneja la lógica de redirección */

class RedirectController
{
    public function redirectToLogin()
    {
        header("Location: app/views/inicio.php");
        exit();
    }
}
