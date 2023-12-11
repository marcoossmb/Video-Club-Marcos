<?php
    class Usuario {
        private $id;
        private $username;
        private $password;
        private $rol;
        
        public function __construct($id, $username, $password, $rol) {
            $this->id = $id;
            $this->username = $username;
            $this->password = $password;
            $this->rol = $rol;
        }

        public function getId() {
            return $this->id;
        }

        public function getUsername() {
            return $this->username;
        }

        public function getPassword() {
            return $this->password;
        }

        public function getRol() {
            return $this->rol;
        }

        public function setId($id): void {
            $this->id = $id;
        }

        public function setUsername($username): void {
            $this->username = $username;
        }

        public function setPassword($password): void {
            $this->password = $password;
        }

        public function setRol($rol): void {
            $this->rol = $rol;
        }
        public function __destruct() {
            echo "El usuario ".$this->getUsername()." ha sido borrado";
        }
    }