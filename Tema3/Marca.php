<?php
class Marca
{
    private $Id;
    private $Nombre;
    private $Sector;
    private $Logo;
    

    public function __get($nombre)
    {
        return $this->$nombre;
    }

  
    public function __set($nombre, $valor)
    {
        $this->$nombre = $valor;
    }
}