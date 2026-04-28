<?php

class Equipo
{
    private $id;
    private $nombre;
    private $puesto;
    private $escudo;

    public function __get($nombre)
    {
        return $this->$nombre;
    }

    public function __set($nombre, $valor)
    {
        $this->$nombre = $valor;
    }
}

?>
