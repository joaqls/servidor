<?php

// Incluimos los Daos

require_once 'DaoProductos.php';
require_once 'DaoMarcas.php';


// Instanciamos  de los objetos DAOS

$daoProd = new DaoProductos("Tema2");
$daoMarc = new DaoMarcas("Tema2");


if (isset($_POST['btnBorrar'])) //Si hemos pulsado borrar producto
{
    
   $id=$_POST['id_borrar'];

   $daoProd->borrar($id);
 
}



if (isset($_POST['btnInsertar'])) // Si hemos pulsado Insertar producto
{
    $pro = new Producto();

    $pro->__set("Nombre", $_POST['nombre']);
    $pro->__set("Marca", $_POST['marca']);
    $pro->__set("Modelo", $_POST['modelo']);
    $pro->__set("Precio", $_POST['precio']);
    $pro->__set("Lanzamiento", $_POST['lanzamiento']);
    
    $nombreFoto = "";

    if ($_FILES['foto']['name']!="" ) //Si hemos seleccionado alguna foto 
    {
        $nombreFoto = $_FILES['foto']['name'];
        move_uploaded_file($_FILES['foto']['tmp_name'], "img/" . $nombreFoto);
    }

    $pro->__set("Foto", $nombreFoto);

    $daoProd->insertar($pro);
    
   
}

// Obtener datos para el listado

 $daoProd->listar();  //Productos a listar

 $daoMarc->listar();  //Marcas para el desplegable

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Tarea 3</title>

   
</head>

<body>

    <!-- Título  -->
    <h1>Listado de Productos</h1>

    <!-- Tabla de productos -->
    <table border="2">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Precio</th>
                <th>Lanzamiento</th>
                <th>Foto</th>
                <th>Borrar</th>
            </tr>
        </thead>
        <tbody>


             <!--Formulario con la fila para el nuevo producto  -->

            <form method="post" action="listado.php" enctype="multipart/form-data">
               
                <input type="submit" name="btnInsertar" value="Insertar">
               
                  <tr>
                    <td></td>
                    <td><input type="text" name="nombre"></td>
                    <td>
                        <select name="marca" >
                            <option value="">--Seleccione una marca----</option>
                            
                            <?php

                                foreach ($daoMarc->marcas  as $marc)
                                { 
                                echo "<option value=".$marc->__get("Id").">".$marc->__get('Nombre')."</option>";
                                }
                            
                            ?>

                        </select>
                    </td>
                    <td><input type="text" name="modelo" ></td>
                    <td><input type="text" name="precio" size="5"></td>
                    <td><input type="date" name="lanzamiento"></td>
                    <td><input type="file" name="foto"></td>
                    
                </tr>
            </form> 

            <!-- Recorre cada producto y lo muestra en una fila de la tabla -->
            <?php 
            
              foreach ($daoProd->productos as $prod)
              {  
                  echo "<tr>";
                  
                        echo " <td>".$prod->__get('Id')."</td>";
                        echo " <td>".$prod->__get('Nombre')."</td>";
                        echo " <td>".$prod->__get('Marca')."</td>";
                        echo " <td>".$prod->__get('Modelo')."</td>";
                        echo " <td>".$prod->__get('Precio')."</td>";
                        echo " <td>".$prod->__get('Lanzamiento')."</td>";
                        echo " <td><img src='img/".$prod->__get('Foto')." width='50'></td>";
                        echo " <td>";
                        
                        echo "    <form method='post' action='listado.php'>";
                        echo "            <input type='hidden' name='id_borrar' value='".$prod->__get('Id') ."'>";
                        echo "           <button type='submit' name='btnBorrar'>Borrar</button>";
                        echo "     </form>";
                        echo  "</td>";

                 echo  "</tr>";

              }  
               
             ?>
            
        </tbody>
    </table>

</body>

</html>