<?php

session_start();

//Borramos las sesiones existentes
$_SESSION = array();
session_destroy();
setcookie("nombre", "", time() - 1);

//Redireccionamiento
header('Location: ../index.php');