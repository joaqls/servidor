<?php

// Clase base de DAO y el objeto
require_once 'LibreriaPDO.php';
require_once 'Producto.php';

// Clase DaoProductos hereda de la clase base DB
class DaoProductos extends DB
{
    
    
    public $productos=array();

    public function __construct($base)
    {
        parent::__construct($base);
    }

   
    public function listar()
    {
        
        $consulta = "SELECT * FROM productos";

        
        $this->productos = array();

        
        $this->ConsultaDatos($consulta);

        // Mapeo de registros a objetos (Encapsulación)
        foreach ($this->filas as $fila) 
        {
            $pro = new Producto();

            $pro->__set("Id", $fila->Id);
            $pro->__set("Nombre", $fila->Nombre);
            $pro->__set("Marca", $fila->Marca);
            $pro->__set("Modelo", $fila->Modelo);
            $pro->__set("Precio", $fila->Precio);
            $pro->__set("Lanzamiento", $fila->Lanzamiento);
            $pro->__set("Foto", $fila->Foto);

            // Añadimos  el objeto al array de productos
            $this->productos[] = $pro;
        }
        
    }

   
    public function borrar($id)
    {
        // Consulta para eliminar el producto por ID
        $consulta = "DELETE FROM productos WHERE Id = :Id";

        // Array de parámetros para proteger contra SQL Injection
        
        $param = array(":Id" => $id);

        // Ejecución a través de la clase base DB
        
        return $this->ConsultaSimple($consulta, $param);
    }

  
    public function insertar($pro)
    {
        // Consulta para insertar el producto
        $consulta = "INSERT INTO productos (Nombre, Marca, Modelo, Precio, Lanzamiento, Foto) 
                     VALUES (:Nombre, :Marca, :Modelo, :Precio, :Lanzamiento, :Foto)";

        // Array de parámetros obtenidos del objeto
        $param = array(
            ":Nombre"      => $pro->__get("Nombre"),
            ":Marca"       => $pro->__get("Marca"),
            ":Modelo"      => $pro->__get("Modelo"),
            ":Precio"      => $pro->__get("Precio"),
            ":Lanzamiento" => $pro->__get("Lanzamiento"),
            ":Foto"        => $pro->__get("Foto")
        );

       
        return $this->ConsultaSimple($consulta, $param);  //Ejecutamos la consulta de insercción
    }
}