<?php

class Actor {

    private $id;
    private $nombre;
    private $apellidos;
    private $fotografia;

    public function __construct($id, $nombre, $apellidos, $fotografia) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->fotografia = $fotografia;
    }

    public function getId() {
        return $this->id;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getApellidos() {
        return $this->apellidos;
    }

    public function getFotografia() {
        return $this->fotografia;
    }

    public function setId($id): void {
        $this->id = $id;
    }

    public function setNombre($nombre): void {
        $this->nombre = $nombre;
    }

    public function setApellidos($apellidos): void {
        $this->apellidos = $apellidos;
    }

    public function setFotografia($fotografia): void {
        $this->fotografia = $fotografia;
    }

    public function __destruct() {
        echo "El actor " . $this->getNombre() . " ha sido borrado";
    }
}
