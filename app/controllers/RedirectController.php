<?php

namespace App\Controllers;

/* Maneja la lógica de redirección */

class RedirectController
{
    public function redirectToLogin()
    {
        header("location:../app/views/login.php");
        exit();
    }
}
