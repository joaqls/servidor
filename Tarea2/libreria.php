<?php 

//Archivo de libreria para operar con mysql

$host="localhost";

$usuario="root";

$clave="";

$base="Tema2";

function Conectar()
{
    global $host,$usuario,$clave,$base;
    
    $db = mysqli_connect($host,$usuario,$clave,$base);
    
    if (!$db)
    {
        echo "Error al conectar";  
        exit();
    }
    
   return $db; 
}

function ConsultaSimple($consulta)   //Metodo para ejecutar consultas que no devuelven filas 
{
    $db=Conectar();  //Nos conectamos a la base de datos
    
    $resul=mysqli_query($db, $consulta);  //Ejecutamos la consulta simple
    
    if (!$resul)  //Si ha devuelto falso es que ha habido un error
    {
        echo "<b>ERROR al ejecutar la consulta".mysqli_error($db);
    }
    
    Cerrar($db);
        
}

function ConsultaDatos($consulta)   //Metodo para ejecutar consultas que SI devuelven filas
{
    $filas=array(); //Array bidimensional(matriz) para almacenar las filas
    
    $db=Conectar();  //Nos conectamos a la base de datos
    
    $resul=mysqli_query($db, $consulta);  //Ejecutamos la consulta simple
    
    while( $fila=mysqli_fetch_assoc($resul) )   //Mientras sigamos extrayendo filas
    {
        $filas[]=$fila; //Guardamos esa fila en la matriz de filas 
    }
   
    Cerrar($db);
   
    return $filas; //Devolvemos el array con las filas
}




function Cerrar($db)
{
  mysqli_close($db);  
}





?>






