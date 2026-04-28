<?php
class Producto
{
    private $Id;
    private $Nombre;
    private $Marca;
    private $Modelo;
    private $Precio;
    private $Lanzamiento;
    private $Foto;

    /**
     * Método GET
     */
    public function __get($nombre)
    {
        return $this->$nombre;
    }

    /**
     * Método SET
     */
    public function __set($nombre, $valor)
    {
        $this->$nombre = $valor;
    }
}