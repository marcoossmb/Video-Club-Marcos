<?php
/**
 * Función para establecer la conexión con la base de datos.
 *
 * @return PDO Retorna un objeto PDO representando la conexión a la base de datos.
 * @throws Exception Redirige a servererror.php en caso de error en la conexión.
 */
function conectarBD() {
    try {
        // Configuración de la conexión a la base de datos
        $cadena_conexion = 'mysql:dbname=videoclub;host=127.0.0.1';
        $usuario = 'root';
        $clave = '';

        // Se crea la conexión con la base de datos
        $bd = new PDO($cadena_conexion, $usuario, $clave);
        return $bd;
    } catch (Exception $e) {
        // En caso de error, redirige a servererror.php
        header("Location: ./servererror.php");
    }
}

/**
 * Función para obtener un usuario de la base de datos.
 *
 * @param PDO $bd Objeto PDO representando la conexión a la base de datos.
 * @param string $user Nombre de usuario.
 * @param string $contrasena Contraseña del usuario.
 * @param string $fecha Fecha de conexión.
 * @return Usuario Retorna un objeto Usuario si se encuentra en la base de datos.
 * @throws Exception Redirige a index.php?error en caso de no encontrar el usuario.
 */
function obtenerUsuario($bd, $user, $contrasena, $fecha) {
    // Consulta SQL para obtener un usuario por nombre de usuario y contraseña
    $sql = "SELECT * FROM usuarios WHERE username = :user AND password = :password";
    $stmt = $bd->prepare($sql);
    $stmt->bindParam(':user', $user);
    $stmt->bindParam(':password', $contrasena);

    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Si se encuentra el usuario, se crea un objeto Usuario y se establecen cookies y sesiones
        foreach ($stmt as $row) {
            $nuevousers = new Usuario($row['id'], $row["username"], $row["password"], $row["rol"]);
        }

        setcookie("nombre", $user, time() + 30 * 24 * 3600, "/");
        setcookie("lastconexion", $fecha, time() + 20 * 24 * 3600, "/");

        $_SESSION['nombre'] = $nuevousers->getUsername();
        $_SESSION['pass'] = $nuevousers->getPassword();
        $_SESSION['rol'] = $nuevousers->getRol();

        return $nuevousers;
    } else {
        // Si no se encuentra el usuario, redirige a index.php?error
        header("Location: ../index.php?error");
    }
}

/**
 * Función para obtener todas las películas de la base de datos.
 *
 * @param PDO $bd Objeto PDO representando la conexión a la base de datos.
 * @return array Retorna un array de objetos Pelicula.
 */
function obtenerPeliculas($bd) {
    $arraypeliculas = array();
    // Consulta SQL para obtener todas las películas
    $sql2 = 'SELECT * FROM peliculas';
    $peliculas = $bd->query($sql2);
    foreach ($peliculas as $linea) {
        // Se crea un objeto Pelicula por cada resultado y se añade al array
        $peliculanueva = new Pelicula($linea["id"], $linea["titulo"], $linea["genero"], $linea["pais"], $linea["anyo"], $linea["cartel"]);
        array_push($arraypeliculas, $peliculanueva);
    }

    return $arraypeliculas;
}

/**
 * Función para obtener los actores de una película específica.
 *
 * @param PDO $bd Objeto PDO representando la conexión a la base de datos.
 * @param Pelicula $pelicula Objeto Pelicula para el cual se obtendrán los actores.
 * @return array Retorna un array de objetos Actor.
 */
function obtenerActoresPorPelicula($bd, $pelicula) {
    // Consulta SQL para obtener los actores de una película
    $sql3 = 'SELECT * FROM actores where id IN (SELECT idActor FROM actuan WHERE idPelicula=' . $pelicula->getId() . ');';
    $actores = $bd->query($sql3);
    $arrayActores = array();

    foreach ($actores as $lineaAct) {
        // Se crea un objeto Actor por cada resultado y se añade al array
        $actor = new Actor($lineaAct["id"], $lineaAct["nombre"], $lineaAct["apellidos"], $lineaAct["fotografia"]);
        array_push($arrayActores, $actor);
    }

    return $arrayActores;
}

/**
 * Función para mostrar los actores de cada película.
 *
 * @param PDO $bd Objeto PDO representando la conexión a la base de datos.
 * @param Pelicula $pelicula Objeto Pelicula para la cual se mostrarán los actores.
 */
function mostrarActoresPorPelicula($bd, $pelicula) {
    echo '<div class="d-flex flex-wrap justify-content-center">';
    // Consulta SQL para obtener los actores de una película
    $sql3 = 'SELECT * FROM actores where id IN (SELECT idActor FROM actuan WHERE idPelicula=' . $pelicula->getId() . ');';
    $actores = $bd->query($sql3);
    foreach ($actores as $lineaAct) {
        // Se crea un objeto Actor por cada resultado y se muestra en formato HTML
        $actor = new Actor($lineaAct["id"], $lineaAct["nombre"], $lineaAct["apellidos"], $lineaAct["fotografia"]);
        echo '<div class="m-3"><p class="mb-0">' . $actor->getNombre() . ' ' . $actor->getApellidos() . '</p>
                    <img class="img__actor mb-4" src="../assets/images/' . $actor->getFotografia() . '" alt="">
              </div>';
    }
}

/**
 * Función para mostrar los actores que no participan en ninguna película.
 *
 * @param PDO $bd Objeto PDO representando la conexión a la base de datos.
 */
function mostrarActoresParo($bd) {
    // Consulta SQL para obtener los actores que no participan en ninguna película
    $sqlParo = 'SELECT * FROM actores a LEFT JOIN actuan ac ON a.id = ac.idActor WHERE ac.idActor IS NULL;';
    $actores = $bd->query($sqlParo);
    foreach ($actores as $actorparo) {
        // Se crea un objeto Actor por cada resultado y se muestra en formato HTML
        $actor = new Actor($actorparo["id"], $actorparo["nombre"], $actorparo["apellidos"], $actorparo["fotografia"]);
        echo '<div class="m-3 d-flex flex-column align-items-center"><p class="mb-0">' . $actor->getNombre() . " " . $actor->getApellidos() . '</p>
              <img class="img__actor mb-4" src="../assets/images/' . $actor->getFotografia() . '" alt=""></div>';
    }
}