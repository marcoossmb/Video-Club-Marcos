<?php

session_start();

if (!$_SESSION["nombre"]) {
    header("Location: ../index.php");
} 
if ($_SESSION["rol"] != 1) {
    header("Location: ./videoclub.php");
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../lib/phpmailer/src/Exception.php';
require '../lib/phpmailer/src/PHPMailer.php';
require '../lib/phpmailer/src/SMTP.php';

if (isset($_POST["send"])) {

    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'videoclubmarcos@gmail.com';
    $mail->Password = 'aqvccjmpaiqfvdet';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom('videoclubmarcos@gmail.com');

    $mail->addAddress('videoclubmarcos@gmail.com');

    $mail->isHTML(true);

    $mail->Subject = $_POST["subjet"];
    $mail->Body = $_POST["mensaje"];

    $mail->send();

    echo
    "
        <script>
        document.location.href = './videoclub.php?correo=true';
        </script>
        ";
}