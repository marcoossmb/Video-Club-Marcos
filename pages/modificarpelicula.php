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

                $titulo = $_POST['titulo'];
                $genero = $_POST['genero'];
                $pais = $_POST['pais'];
                $anyo = $_POST['anyo'];
                $cartel = $_POST['cartel'];
                
                // Eliminar la pelicula
                $cadena_conexion = 'mysql:dbname=videoclub;host=127.0.0.1';
                $usuariobd = 'root';
                $clavebd = '';

                try {
                    // Se crea la conexión con la base de datos
                    $bd = new PDO($cadena_conexion, $usuariobd, $clavebd);

                    // MOdifica la película
                    $sqlUpdate = "UPDATE peliculas SET titulo = '$titulo', genero = '$genero', pais = '$pais', anyo = '$anyo', cartel = '$cartel' WHERE id = :id;";                    
                    $stmtUpdate = $bd->prepare($sqlUpdate);
                    $stmtUpdate->bindParam(':id', $_GET["modi"]);
                    $stmtUpdate->execute();

                    header("Location: ./videoclub.php");
                } catch (Exception $e) {
                    echo "Error al hacer el update: " . $e->getMessage();
                }
            } else {
                // Mostrar el formulario de confirmación
                ?>                               
                <!-- INICIO MODAL MODIFICAR -->
                <div class="modal fade show d-flex" id="exampleModal" tabindex="-1" aria-labelledby="exampleModal" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Modificar Película</h1>
                            </div>
                            <div class="modal-body">
                                <form class="box__peliculas d-flex flex-column align-items-center" method="post" action="./modificarpelicula.php?modi=<?php echo $_GET["modi"]; ?>">
                                    <div class="mt-3">
                                        <label>Título:</label>
                                        <input class="form-control outline-0 text-dark" type="text" name="titulo" required>
                                    </div>
                                    <div class="mt-3">
                                        <label>Género:</label>
                                        <input class="form-control outline-0 text-dark" type="text" name="genero" required>
                                    </div>
                                    <div class="mt-3">
                                        <label>Pais:</label>
                                        <input class="form-control outline-0 text-dark" type="text" name="pais" required>
                                    </div>
                                    <div class="mt-3">
                                        <label>Año:</label>
                                        <input class="form-control outline-0 text-dark" type="number" name="anyo" required>
                                    </div>
                                    <div class="mt-3">
                                        <label>Cartel:</label>
                                        <input class="form-control outline-0 text-dark" type="text" name="cartel" placeholder="toy_story.jpg" required>
                                    </div>
                                    <div class="mt-3">
                                        <a href="./videoclub.php" class="btn btn-secondary">Cerrar</a>
                                        <button type="submit" class="btn btn-primary">Modificar</button>
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
    </body>
</html>