<?php

if (!$_SESSION["nombre"]) {
    header("Location: ../index.php");
}
if ($_SESSION["rol"] != 1) {
    header("Location: ./videoclub.php");
}

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

        //Sacar un nuevo id
        $sqlId = "SELECT MAX(id) AS id FROM peliculas;";

        $stmtId = $bd->prepare($sqlId);
        $stmtId->execute();

        $id;
        foreach ($stmtId as $row) {
            $id = $row["id"];
        }
        $id += 1;

        // Añadir la película
        $sqlAniadir = 'INSERT INTO peliculas (id, titulo, genero, pais, anyo, cartel) VALUES ("' . $id . '","' . $titulo . '","' . $genero . '","' . $pais . '",' . $anyo . ',"' . $cartel . '");';
        $stmtAniadir = $bd->prepare($sqlAniadir);
        $stmtAniadir->execute();

        $buclerand = rand(1, 6);
        for ($i = 1; $i <= $buclerand; $i++) {

            //Sacar actores        
            $sqlIdActor = "SELECT id FROM actores ORDER BY RAND() LIMIT 1;";
            $stmtIdActor = $bd->prepare($sqlIdActor);

            if ($stmtIdActor->rowCount() == 0) {
                $stmtIdActor->execute();

                $idActor;
                foreach ($stmtIdActor as $row) {
                    $idActor = $row["id"];
                }

                // Añadir actores
                $actorrand = rand(1, $idActor);

                $sqlActor = 'INSERT INTO actuan (idPelicula, idActor) VALUES ("' . $id . '","' . $actorrand . '");';
                $stmtActor = $bd->prepare($sqlActor);
                $stmtActor->execute();
            }
        }
        header("Location: ./videoclub.php");
    } catch (Exception $e) {
        header("Location: ./videoclub.php");
    }
}