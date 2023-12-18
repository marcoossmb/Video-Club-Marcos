<?php
// Se incluyen los archivos necesarios
include '../lib/model/usuario.php';
include '../lib/model/pelicula.php';
include '../lib/model/actor.php';
include '../functions/funciones.php';

session_start();

// Se obtiene la fecha actual
$fecha = date('d-m-Y H:i:s');

try {
    // Detalles de la conexión a la base de datos
    $cadena_conexion = 'mysql:dbname=videoclub;host=127.0.0.1';
    $usuario = 'root';
    $clave = '';

    // Se crea la conexión con la base de datos
    $bd = new PDO($cadena_conexion, $usuario, $clave);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user = $_POST['user'];
        $contrasena = hash("sha256", $_POST['password']);
    } else {
        // Si la sesión ya está iniciada, se obtienen los datos de la sesión
        $user = $_SESSION['nombre'];
        $contrasena = $_SESSION['pass'];
    }

    // Obtiene información del usuario desde la base de datos
    $nuevousers = obtenerUsuario($bd, $user, $contrasena, $fecha);

    // Obtiene un array de películas desde la base de datos
    $arraypeliculas = obtenerPeliculas($bd);
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
                    <h1 class="text-center pt-4">Bienvenido <?php echo ucfirst($_SESSION['nombre']) ?></h1>
                </header>
                <!-- FIN DEL HEADER -->

                <!-- INICIO DEL MAIN -->
                <main class="main">
                    <a href="./cerrarSesion.php" class="link">Cerrar Sesión</a>
                    <p class="lastconection"> <?php echo 'Última conexión: ' . $fecha ?> </p>
                    <h2 class="ms-5">Películas</h2>
                    <div class="d-flex flex-wrap box__peliculas">
                        <?php
                        foreach ($arraypeliculas as $pelicula) {
                            ?>
                            <div class="box__img border">
                                <img class="img__pelicula" src="../assets/images/<?php echo $pelicula->getCartel() ?>" alt="">
                                <p class="mt-3"><?php echo $pelicula->getTitulo() ?> </p>
                                <p><?php echo "Año: " . $pelicula->getAnyo() ?></p>                                
                                <?php
                                mostrarActoresPorPelicula($bd, $pelicula);
                                ?>
                            </div>
                            <?php
                            if ($nuevousers->getRol() == 1) {
                                ?>
                                <div>
                                    <?php
                                    $_SESSION["idPeli"] = $pelicula->getId();
                                    $_SESSION["titlePeli"] = $pelicula->getTitulo();
                                    ?>
                                    <a href="./modificarpelicula.php?modi=<?php echo $_SESSION["idPeli"] ?>" class="p-1 ps-2 mt-3 btn__aniadir me-1">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    <a href="./eliminarpelicula.php?elimi=<?php echo $_SESSION["idPeli"] ?>&title=<?php echo urlencode($_SESSION["titlePeli"]); ?>" class="p-1 pe-3 ps-3 pt-2 mt-3 btn__borrar">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                                <?php
                            }
                            ?>
                    </div>
                        <?php
                    }
                    ?>
                    </div>
                </main>
                <!-- FIN DEL MAIN -->

                <div class="d-flex flex-column box__peliculas align-items-center">
                    <h3>Actores en paro</h3>
                    <div class="d-flex flex-wrap">
                        <?php
                        mostrarActoresParo($bd);
                        ?>
                    </div>
                </div>
                <?php
                if ($nuevousers->getRol() != 1) {
                    ?>
                    <div class="mt-5 mb-5 pb-5 d-flex align-items-center justify-content-evenly border-top pt-5">                    
                        <!-- INICIO FORM CORREOS -->
                        <form class="ml-5" method="post" action="./sendemail.php">
                            <?php
                            if (isset($_GET['correo'])) {
                                echo '<p class="text-white bg-success bg-gradient bg-opacity-75 box__peliculas rounded p-1">Correo enviado correctamente</p>';
                            }
                            ?>
                            <h2 class="form__h2">Enviar Incidencia</h2><br>
                            <label class="box__peliculas">Asunto</label><br>
                            <input class="form-control outline-0" type="text" name="subjet"> <br>
                            <label class="box__peliculas">Mensaje</label><br>
                            <textarea class="form-control outline-0" name="mensaje" rows="4" cols="50"></textarea><br>
                            <button class="mt-3 p-2 form__btn d-flex justify-content-center border-0 rounded" type="submit" value="" name="send">Enviar</button>
                        </form>
                        <!-- FIN FORM CORREOS -->
                        <div>
                            <img class="form__img" src="../assets/images/incidencia.jpeg" alt="alt"/>
                        </div>
                    </div>
                    <?php
                } else {
                    ?>
                    <!-- INICIO FORM AÑADIR -->
                    <div class="mt-5 mb-5 pb-5 d-flex flex-column align-items-center border-top pt-5">
                        <h2 class="mb-4">Añadir Película</h2>
                        <form class="box__peliculas d-flex flex-wrap justify-content-evenly" method="post" action="./aniadirpelicula.php">
                            <div class="me-5 ms-5">
                                <label>Título:</label>
                                <input class="form-control outline-0" type="text" name="titulo" required>
                            </div>
                            <div class="me-5">
                                <label>Género:</label>
                                <input class="form-control outline-0" type="text" name="genero" required>
                            </div>
                            <div class="me-5">
                                <label>Pais:</label>
                                <input class="form-control outline-0" type="text" name="pais" required>
                            </div>
                            <div class="me-3 ms-5">
                                <label>Año:</label>
                                <input class="form-control outline-0" type="number" name="anyo" required>
                            </div>
                            <div class="me-3">
                                <label>Cartel:</label>
                                <input class="form-control outline-0 text-dark" type="text" name="cartel" placeholder="cars.jpg" required>
                            </div>
                            <button class="mt-3 btn__aniadir bg-success" type="submit">+</button>
                        </form>
                        <!-- FIN FORM AÑADIR -->
                    </div>
                    <?php
                }
                ?>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
        </body>
    </html>

    <?php
// Se cierra la conexión
    $bd = null;
} catch (Exception $e) {
    header("Location: ./servererror.php");
}  