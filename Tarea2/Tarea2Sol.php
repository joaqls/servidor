
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Mostrar productos</title>
</head>

<?php //http://localhost/SolElearning/Tarea2/Tarea2Sol.php


include_once("libreria.php");

$marcas = ConsultaDatos("SELECT Id, Nombre FROM marcas ORDER BY Nombre");

$idMarca = "";

if (isset($_POST["btnMostrar"])) 
{
    $idMarca = $_POST["marca"];
}


?>



<body>

<h2>Mostrar productos por marca</h2>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
    <label>Marca: </label>
    <select name="marca">
        <?php
        foreach ($marcas as $fila) 
        {

            echo "<option value='$fila[Id]' ";

            if ($fila["Id"] == $idMarca)    // Si la marca coincide con la seleccinada previamente
            {
                echo " selected ";          // Añadimos el atributo selected
            } 
           
            echo " >$fila[Nombre]</option>";
            
        }
        ?>
    </select>
    <input type="submit" name="btnMostrar" value="Mostrar">
</form>

    <?php
    
    if (isset($_POST["btnMostrar"])) 
    {

        $consulta = "SELECT Id, Nombre, Marca, Modelo, Precio, Lanzamiento
                    FROM productos
                    WHERE Marca=".$idMarca."
                    ORDER BY Nombre";

        $productos = ConsultaDatos($consulta);
    
         echo " <h3>Productos de la marca seleccionada</h3>";

            if (count($productos) == 0) 
            {
                echo "<p>No hay productos para esa marca.</p>";
            } 
            else 
            {
               echo "<table border='2' >";
               echo "<tr>";
               echo "<th>Id</th>";
               echo "<th>Nombre</th>";
               echo "<th>Marca</th>";
               echo "<th>Modelo</th>";
               echo "<th>Precio</th>";
               echo "<th>Lanzamiento</th>";
               echo "</tr>";
                
                    foreach ($productos as $producto) 
                    {
                        echo "<tr>";
                        echo "<td>".$producto["Id"]."</td>";
                        echo "<td>".$producto["Nombre"]."</td>";
                        echo "<td>".$producto["Marca"]."</td>";
                        echo "<td>".$producto["Modelo"]."</td>";
                        echo "<td>".$producto["Precio"]."</td>";
                        echo "<td>".$producto["Lanzamiento"]."</td>";
                        echo "</tr>";
                    }
                
                echo "</table>";
            
            }
        
    }
   
   
   ?>

</body>
</html>
