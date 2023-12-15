<?php
include '../lib/model/usuario.php';
include '../lib/model/pelicula.php';
include '../lib/model/actor.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $cadena_conexion = 'mysql:dbname=videoclub;host=127.0.0.1';
        $usuario = 'root';
        $clave = '';

        // Se crea la conexión con la base de datos
        $bd = new PDO($cadena_conexion, $usuario, $clave);

        // Verificar credenciales de usuario
        $user = $_POST['user'];
        $contrasena = hash("sha256", $_POST['password']);
        $contrasena2 = $_POST['password'];

        $sql = "SELECT * FROM usuarios WHERE username = :user AND password = :password";
        $stmt = $bd->prepare($sql);
        $stmt->bindParam(':user', $user);
        $stmt->bindParam(':password', $contrasena);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            foreach ($stmt as $row) {
                $_SESSION['username'] = $row['username'];
                $nuevousers = new Usuario($row['id'], $row["username"], $row["password"], $row["rol"]);
            }
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
                                <div class="box__img border">
                                    <img class="img__pelicula" src="../assets/images/<?php echo $pelicula->getCartel() ?>" alt="">
                                    <p class="mt-3"><?php echo $pelicula->getTitulo() ?> </p>
                                    <p><?php echo "Año: " . $pelicula->getAnyo() ?></p>

                                    <?php
                                    echo 'Actor/es:<br>';
                                    echo '<br>';
                                    $sql3 = 'SELECT * FROM actores where id IN (SELECT idActor FROM actuan WHERE idPelicula=' . $pelicula->getId() . ');';
                                    $actores = $bd->query($sql3);
                                    foreach ($actores as $lineaAct) {
                                        $actor = new Actor($lineaAct["id"], $lineaAct["nombre"], $lineaAct["apellidos"], $lineaAct["fotografia"]);
                                        echo '<p class="mb-0">' . $actor->getNombre() . " " . $actor->getApellidos() . "</p><br>";
                                        ?>
                                        <br><img class="img__actor mb-4" src="../assets/images/<?php echo $actor->getFotografia() ?>" alt=""><br>
                                        <?php
                                    }

                                    if ($nuevousers->getRol() == 1) {
                                        ?>
                                        <button type="button" class="mt-3 btn__aniadir" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                            <i class="fa-solid fa-pencil"></i>
                                        </button>
                                        <button type="button" class="mt-3 btn__borrar" data-bs-toggle="modal" data-bs-target="#exampleModal2" data-id="<?php echo $pelicula->getId(); ?>">
                                            -
                                        </button><br>
                                        <!-- INICIO MODAL BORRAR -->
                                        <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModal2" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Eliminar Película</h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        ¿Estás seguro de que desea eliminar esta película?
                                                        <?php echo '<br>'.$pelicula->getTitulo() ?>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                        <a href="../pages/eliminarpelicula.php?id=<?php echo $pelicula->getId(); ?>" type="button" class="btn btn-danger">Eliminar</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- FIN MODAL BORRAR -->
                                        
                                        <!-- INICIO MODAL MODIFICAR -->
                                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModal" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Modificar Película</h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-">
                                                        <form class="box__peliculas d-flex flex-column align-items-center" method="post" action="">
                                                            <div class="mt-3">
                                                                <label>Título:</label>
                                                                <input class="form-control outline-0" type="text" name="titulo" required>
                                                            </div>
                                                            <div class="mt-3">
                                                                <label>Género:</label>
                                                                <input class="form-control outline-0" type="text" name="genero" required>
                                                            </div>
                                                            <div class="mt-3">
                                                                <label>Pais:</label>
                                                                <input class="form-control outline-0" type="text" name="pais" required>
                                                            </div>
                                                            <div class="mt-3">
                                                                <label>Año:</label>
                                                                <input class="form-control outline-0" type="text" name="anyo" required>
                                                            </div>
                                                            <div class="mt-3">
                                                                <label>Cartel:</label>
                                                                <input class="form-control outline-0" type="text" name="cartel" placeholder="cartel.jpg" required>
                                                            </div>
                                                            <div class="mt-3 mb-3">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                                <button type="button" class="btn btn-primary">Modificar</button>
                                                            </div>
                                                        </form>
                                                    </div>                                                   
                                                </div>
                                            </div>
                                        </div>
                                        <!-- FIN MODAL MODIFICAR -->
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                        if ($nuevousers->getRol() != 1) {
                            ?>
                            <div class="mt-5 mb-5 pb-5 d-flex justify-content-evenly border-top pt-5">
                                <!-- INICIO FORM CORREOS -->
                                <form class="ml-5" method="post" action="./sendemail.php">
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
                                    <button class="mt-3 btn__aniadir bg-success" type="submit">+</button>
                                </form>
                                <!-- FIN FORM AÑADIR -->
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
} else {
    header("Location: ../index.php");
}