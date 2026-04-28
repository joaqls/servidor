<?php

class DB {
    
    //Propiedades para establecer la conexion a la BBDD
    
    private $host="localhost";
    
    private $usuario="root";
    
    private $clave="";
    
    private $base;
    
    //Propiedades operativas de la clase
    
    private $pdo; 
    
    public $filas=array();
    
    public function __construct($base)   
    {
      $this->base=$base;
    }
    
    
    private function Conectar()    //Metodo para establecer la conexión con la BBDD
    {
        try {
            
            $this->pdo = new PDO("mysql:host=$this->host;dbname=$this->base",$this->usuario, $this->clave);  //Instanciamos el objeto PDO que establece la conexión
            
            $this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
        }
        
       
    }
    
    public function ConsultaSimple($consulta,$param=array())   //Metodo para ejecutar consultas simples(no devuelven datos)
    {
        
        $this->Conectar();
        
        $sta=$this->pdo->prepare($consulta);
        
        $resul=$sta->execute($param);  //Guardamos en una variable el resultado de la ejecución de la consulta(TRUE/FALSE)
          
        if ( !$resul )
        {
          echo "Error en la consulta:".$this->pdo->errorInfo()  ;
         
        }
       
        $this->Cerrar();
        
        return $resul;  //Retornamos si la consulta se ha podido ejecutar correctamente
        
    }
    
    public function ConsultaDatos($consulta,$param=array())   //Metodo para ejecutar consultas que devuelven datos)
    {
        
        $this->Conectar();
        
        $this->filas=array();  //HAy que vaciar el array de filas tras cada consulta de datos
        
        $sta=$this->pdo->prepare($consulta);
        
        if ( !$sta->execute($param) )
        {
            echo "Error en la consulta:".$this->pdo->errorInfo()  ;
        }
        else //Hay que recuperar los datos
        {
           $this->filas=$sta->fetchAll(PDO::FETCH_OBJ);
            
        }
            
        $this->Cerrar();
       
    }
    
    
    
    private function Cerrar()    //Metodo para establecer la conexión con la BBDD
    {
       $this->pdo=NULL; 
    }
}







?>