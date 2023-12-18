<?php
session_start();

if (!$_SESSION["nombre"]) {
    header("Location: ../index.php");
}
if ($_SESSION["rol"] != 1) {
    header("Location: ./videoclub.php");
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>VideoClub • Marcos</title>
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <link rel="shortcut icon" href="../assets/images/logotipo.jpg" type="image/x-icon">
    </head>
    <body class="body">
        <div class=" p-3">
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {

                // Eliminar la pelicula
                $cadena_conexion = 'mysql:dbname=videoclub;host=127.0.0.1';
                $usuariobd = 'root';
                $clavebd = '';

                try {
                    // Se crea la conexión con la base de datos
                    $bd = new PDO($cadena_conexion, $usuariobd, $clavebd);

                    // Elimina la relación actúan
                    $sql = "DELETE FROM actuan WHERE idPelicula = :id";
                    $borrarActuan = $bd->prepare($sql);
                    $borrarActuan->bindParam(':id', $_GET["elimi"]);
                    $borrarActuan->execute();

                    // Elimina la película
                    $sqlElimina = "DELETE FROM peliculas WHERE id = :id";
                    $stmtEliminar = $bd->prepare($sqlElimina);
                    $stmtEliminar->bindParam(':id', $_GET["elimi"]);
                    $stmtEliminar->execute();

                    header("Location: ./videoclub.php");
                } catch (Exception $e) {
                    echo "Error al hacer el delete: " . $e->getMessage();
                }
            } else {
                // Mostrar el formulario de confirmación
                ?>                               
                <!-- INICIO MODAL BORRAR -->
                <div class="modal fade show d-flex">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" >Eliminar Película</h1>
                            </div>
                            <div class="modal-body">
                                <?php
                                if (!isset($_GET["elimi"]) || !isset($_GET["title"])) {
                                    echo
                                    '<p class="text-center fw-bold text-danger">Ninguna pelicula seleccionada</p>';
                                } else {
                                    echo
                                    '¿Estás seguro de que desea eliminar esta película?
                                <h4 class="text-center fw-bold">' . $_GET["title"] . '</h4>';
                                }
                                ?>
                            </div>
                            <?php
                            if (isset($_GET["elimi"]) && isset($_GET["title"])) {
                                ?>
                                <form class="modal-footer" action="./eliminarpelicula.php?elimi=<?php echo $_GET["elimi"]; ?>" method="post">
                                    <a href="./videoclub.php" class="btn btn-secondary">Cerrar</a>
                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                </form>
                                <?php
                            } else {
                                echo '<a href="./videoclub.php" class="btn btn-secondary">Cerrar</a>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <!-- FIN MODAL BORRAR -->
                <?php
            }
            ?>
        </div>    
    </body>
</html>