<?php

require 'app/controllers/RedirectController.php';

use App\Controllers\RedirectController;

$redirectController = new RedirectController();
$redirectController->redirectToLogin();
