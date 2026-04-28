<?php

require_once 'LibreriaPDO.php';
require_once 'Marca.php';

// Clase DaoMarcas hereda de la clase base DB

class DaoMarcas extends DB
{

    public $marcas=array(); //Array de objetos tipo marcas

    public function __construct($base)
    {
        parent::__construct($base);
    }

    
    public function listar()
    {
        // Consulta de todas las marcas

        $consulta = "SELECT * FROM marcas ORDER BY Nombre ASC";
        
        $this->ConsultaDatos($consulta);  //Mandamos las consulta a la BBDD

        foreach($this->filas as $fila)
        {
             $marca= new Marca();

             $marca->__set("Id", $fila->Id);
             $marca->__set("Nombre",$fila->Nombre );
             $marca->__set("Sector",$fila->Sector );
             $marca->__set("Logo", $fila->Logo);
   
             $this->marcas[]=$marca;
             

        }

    }
}