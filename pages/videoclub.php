<?php
include '../lib/model/actor.php';
include '../lib/model/pelicula.php';
include '../lib/model/usuario.php';

session_start();

$cadena_conexion = 'mysql:dbname=videoclub;host=127.0.0.1';
$usuario = 'root';
$clave = '';

try {
    // Se crea la conexión con la base de datos
    $bd = new PDO($cadena_conexion, $usuario, $clave);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user = $_POST['user'];
        $contrasena = hash("sha256", $_POST['password']);

        $sql = 'SELECT username,password,rol FROM usuarios where username="' . $user . '"and password="' . $contrasena . '"';
        $result = $bd->query($sql);

        if ($result->rowCount() > 0) {
            foreach ($result as $row) {
                $_SESSION['username'] = $row['username'];
            }
        } else {
            header("Location: ../index.php?error");
        }
    } else {
        header("Location: ../index.php");
    }

    $arraypeliculas = array();
    $sql2 = 'SELECT * FROM peliculas';
    $peliculas = $bd->query($sql2);
    foreach ($peliculas as $linea) {
        $pelicula = new Pelicula($linea["id"], $linea["titulo"], $linea["genero"], $linea["pais"], $linea["anyo"], $linea["cartel"]);
        array_push($arraypeliculas, $pelicula);
    }
    ?>
    <!DOCTYPE html>
    <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="shortcut icon" href="../assets/images/logotipo.jpg" type="image/x-icon">
            <link rel="stylesheet" href="../css/videoclub.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
            <title>VideoClub • Marcos</title>
        </head>
        <body class="body">
            <div class="contenedor">
                <!-- INICIO DEL HEADER -->
                <header>
                    <h1 class="text-center pt-4">Bienvenido <?php echo ucfirst($_SESSION['username']) ?></h1>
                </header>
                <!-- FIN DEL HEADER -->

                <!-- INICIO DEL MAIN -->
                <main class="main">
                    <a href="../index.php" class="link">Cerrar Sesión</a>
                    <h2 class="ms-5">Películas</h2>
                    <div class="d-flex flex-wrap box__peliculas justify-content-center">
                        <?php
                        foreach ($arraypeliculas as $pelicula) {
                            ?>
                            <div class="box__img">
                                <img class="img__pelicula" src="../assets/images/<?php echo $pelicula->getCartel() ?>" alt="">
                                <p class="mt-3"><?php echo $pelicula->getTitulo() ?> </p>
                                <p><?php echo "Año: " . $pelicula->getAnyo() ?></p>

                                <?php
                                $arrayactores = array();
                                $sql3 = 'SELECT * FROM actores WHERE id = ' . $pelicula->getId();
                                $actores = $bd->query($sql3);
                                foreach ($actores as $lineaAct) {
                                    $actor = new Actor($lineaAct["id"], $lineaAct["nombre"], $lineaAct["apellidos"], $lineaAct["fotografia"]);
                                    echo 'Actor/es:';
                                    echo $actor->getNombre() . " " . $actor->getApellidos() . "<br>";
                                    ?>
                                    <br><img class="img__actor" src="../assets/images/<?php echo $actor->getFotografia() ?>" alt="">
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                    if ($_SESSION['username'] != "admin") {
                        ?>
                        <div class="mt-5 mb-5 pb-5 d-flex justify-content-evenly border-top pt-5">
                            <form class="ml-5" method="post" action="">
                                <h2 class="form__h2">Enviar Incidencia</h2><br>
                                <label class="box__peliculas">Asunto</label><br>
                                <input class="form-control outline-0" type="text" name="subjet"> <br>
                                <label class="box__peliculas">Mensaje</label><br>
                                <textarea class="form-control outline-0" name="mensaje" rows="4" cols="50"></textarea><br>
                                <button class="mt-3 p-2 form__btn d-flex justify-content-center border-0 rounded" type="submit" value="" name="send">Enviar</button>
                            </form>
                            <div>
                                <img class="form__img" src="../assets/images/incidencia.jpeg" alt="alt"/>
                            </div>
                        </div>
                        <?php
                    } else {
                        ?>
                        <!-- INICIO FORM AÑADIR -->
                        <div class="mt-5 mb-5 pb-5 d-flex flex-column align-items-center border-top pt-5">
                            <h2 class="mb-4">Añadir o Modificar Película</h2>
                            <form class="box__peliculas d-flex flex-wrap" method="post" action="">
                                <div class="me-3 ms-5">
                                    <label>Título:</label>
                                    <input class="form-control outline-0" type="text" name="titulo" required>
                                </div>
                                <div class="me-3">
                                    <label>Género:</label>
                                    <input class="form-control outline-0" type="text" name="genero" required>
                                </div>
                                <div class="me-3">
                                    <label>Pais:</label>
                                    <input class="form-control outline-0" type="text" name="pais" required>
                                </div>
                                <div class="me-3">
                                    <label>Año:</label>
                                    <input class="form-control outline-0" type="text" name="anyo" required>
                                </div>
                                <button class="mt-3 btn__aniadir" type="submit"><i class="fa-solid fa-pencil"></i></button>
                            </form>
                            <!-- FIN FORM AÑADIR -->
                            
                            <!-- INICIO FORM BORRAR -->
                            <h2 class="mb-4 mt-5">Eliminar Película</h2>
                            <form class="box__peliculas d-flex flex-wrap" method="post" action="">
                                <div class="me-3 ms-5">
                                    <label>¿Cúal es el nombre de la película que desea eliminar?</label>
                                    <input class="form-control outline-0" type="text" name="titulo" required>
                                </div>
                                <button class="mt-3 btn__borrar" type="submit">-</button>
                            </form>
                            <!-- FIN FORM BORRAR -->
                        </div>
                        <?php
                    }
                    ?>
                </main>
                <!-- FIN DEL MAIN -->
            </div> 
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
        </body>
    </html>
    <?php
    // Se cierra la conexión
    $bd = null;
} catch (Exception $e) {
    echo "Error con la base de datos: " . $e->getMessage();
}
?>