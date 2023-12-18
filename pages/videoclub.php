<?php
include '../lib/model/usuario.php';
include '../lib/model/pelicula.php';
include '../lib/model/actor.php';

session_start();

$fecha = date("d/m/Y | H:i:s");

try {
    $cadena_conexion = 'mysql:dbname=videoclub;host=127.0.0.1';
    $usuario = 'root';
    $clave = '';

    // Se crea la conexión con la base de datos
    $bd = new PDO($cadena_conexion, $usuario, $clave);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user = $_POST['user'];
        $contrasena = hash("sha256", $_POST['password']);
    } else {
        $user = $_SESSION['nombre'];
        $contrasena = $_SESSION['pass'];
    }

    $sql = "SELECT * FROM usuarios WHERE username = :user AND password = :password";
    $stmt = $bd->prepare($sql);
    $stmt->bindParam(':user', $user);
    $stmt->bindParam(':password', $contrasena);

    $stmt->execute();

    if ($stmt->rowCount() > 0) {

        foreach ($stmt as $row) {
            $nuevousers = new Usuario($row['id'], $row["username"], $row["password"], $row["rol"]);
        }

        setcookie("nombre", $user, time() + 30 * 24 * 3600, "/");
        setcookie("lastconexion", $fecha, time() + 20 * 24 * 3600, "/");

        $_SESSION['nombre'] = $nuevousers->getUsername();
        $_SESSION['pass'] = $nuevousers->getPassword();
        $_SESSION['rol'] = $nuevousers->getRol();
    } else {
        header("Location: ../index.php?error");
    }

    $arraypeliculas = array();
    $sql2 = 'SELECT * FROM peliculas';
    $peliculas = $bd->query($sql2);
    foreach ($peliculas as $linea) {
        $peliculanueva = new Pelicula($linea["id"], $linea["titulo"], $linea["genero"], $linea["pais"], $linea["anyo"], $linea["cartel"]);
        array_push($arraypeliculas, $peliculanueva);
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
                    <h1 class="text-center pt-4">Bienvenido <?php echo ucfirst($_SESSION['nombre']) ?></h1>
                </header>
                <!-- FIN DEL HEADER -->

                <!-- INICIO DEL MAIN -->
                <main class="main">
                    <a href="./cerrarSesion.php" class="link">Cerrar Sesión</a>
                    <h2 class="ms-5">Películas</h2>
                    <div class="d-flex flex-wrap box__peliculas">
                        <?php
                        foreach ($arraypeliculas as $pelicula) {
                            ?>
                            <div class="box__img border">
                                <img class="img__pelicula" src="../assets/images/<?php echo $pelicula->getCartel() ?>" alt="">
                                <p class="mt-3"><?php echo $pelicula->getTitulo() ?> </p>
                                <p><?php echo "Año: " . $pelicula->getAnyo() ?></p>
                                <div class="d-flex flex-wrap justify-content-center">
                                    <?php
                                    $sql3 = 'SELECT * FROM actores where id IN (SELECT idActor FROM actuan WHERE idPelicula=' . $pelicula->getId() . ');';
                                    $actores = $bd->query($sql3);
                                    foreach ($actores as $lineaAct) {
                                        $actor = new Actor($lineaAct["id"], $lineaAct["nombre"], $lineaAct["apellidos"], $lineaAct["fotografia"]);
                                        echo '<div class="m-3"><p class="mb-0">' . $actor->getNombre() . " " . $actor->getApellidos() . "</p>";
                                        ?>
                                        <img class="img__actor mb-4" src="../assets/images/<?php echo $actor->getFotografia() ?>" alt="">
                                    </div>
                                    <?php
                                }
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
                                    <a href="./eliminarpelicula.php?elimi=<?php echo $_SESSION["idPeli"] ?>&&title=<?php echo $_SESSION["titlePeli"] ?>" class="p-1 pe-3 ps-3 pt-2 mt-3 btn__borrar">
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
                </main>
                <div class="d-flex flex-column box__peliculas align-items-center">
                    <h3>Actores en paro</h3>
                    <div class="d-flex flex-wrap">
                        <?php
                        $sqlParo = 'SELECT * FROM actores a LEFT JOIN actuan ac ON a.id = ac.idActor WHERE ac.idActor IS NULL;';
                        $actores = $bd->query($sqlParo);
                        foreach ($actores as $actorparo) {
                            $actor = new Actor($actorparo["id"], $actorparo["nombre"], $actorparo["apellidos"], $actorparo["fotografia"]);
                            echo '<div class="m-3 d-flex flex-column align-items-center"><p class="mb-0">' . $actor->getNombre() . " " . $actor->getApellidos() . '</p>
                                      <img class="img__actor mb-4" src="../assets/images/' . $actor->getFotografia() . '" alt=""></div>';
                            ?>

                            <?php
                        }
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
                                <input class="form-control outline-0 text-dark" type="text" name="cartel" placeholder="toy_story.jpg" required>
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
    echo "Error con la base de datos: " . $e->getMessage();
}  