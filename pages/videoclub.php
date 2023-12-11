<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['user'];
    $contrasena = hash("sha256", $_POST['password']);

    $cadena_conexion = 'mysql:dbname=videoclub;host=127.0.0.1';
    $usuario = 'root';
    $clave = '';

    try {
        //Se crea la conexión con la base de datos
        $bd = new PDO($cadena_conexion, $usuario, $clave);
        $sql = 'SELECT username,password,rol FROM usuarios where username="' . $user . '"and password="' . $contrasena . '"';
        $user_result = $bd->query($sql);

        if ($user_result->rowCount() > 0) {
            foreach ($user_result as $row) {
                $_SESSION['username'] = $row['username'];
            }
        } else {
            header("Location: ../index.php?error");
        }

        //Se cierra la conexión
        $bd = null;
    } catch (Exception $e) {
        echo "Error con la base de datos: " . $e->getMessage();
    }
} else {
    header("Location: ../index.php");
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="../assets/images/logotipo.jpg" type="image/x-icon">
        <link rel="stylesheet" href="../css/videoclub.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <title>VideoClub • Marcos</title>
    </head>
    <body class="body">
        <div class="contenedor">
            <!-- INICIO DEL HEADER -->
            <header>
                <h1>Bienvenido <?php echo $_SESSION['username'] ?></h1>
            </header>
            <!-- FIN DEL HEADER -->

            <!-- INICIO DEL MAIN -->
            <main class="main">

            </main>
            <!-- FIN DEL MAIN -->
        </div> 
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </body>
</html>