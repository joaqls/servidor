<?php

require_once 'DB.php';
require_once 'Equipo.php';

class DaoEquipos extends DB
{
    public $equipos = array();

    public function __construct()
    {
        parent::__construct("equipos");
    }

    // Devuelve todos los equipos ordenados por puesto ascendente
    public function listar()
    {
        $this->equipos = array();

        $this->ConsultaDatos("SELECT id, nombre, puesto, escudo FROM equipos ORDER BY puesto ASC");

        foreach ($this->filas as $fila)
        {
            $eq = new Equipo();
            $eq->__set("id",     $fila["id"]);
            $eq->__set("nombre", $fila["nombre"]);
            $eq->__set("puesto", $fila["puesto"]);
            $eq->__set("escudo", $fila["escudo"]);
            $this->equipos[] = $eq;
        }
    }

    // Inserta un nuevo equipo al final de la clasificación
    public function insertar($eq)
    {
        // Calcular el siguiente puesto disponible
        $this->ConsultaDatos("SELECT COALESCE(MAX(puesto), 0) + 1 AS nextPuesto FROM equipos");
        $nextPuesto = $this->filas[0]["nextPuesto"];

        $consulta = "INSERT INTO equipos (nombre, puesto, escudo) VALUES (:nombre, :puesto, :escudo)";

        $param = array(
            ":nombre" => $eq->__get("nombre"),
            ":puesto" => $nextPuesto,
            ":escudo" => $eq->__get("escudo")
        );

        $this->ConsultaSimple($consulta, $param);
    }

    // Elimina un equipo por su id
    public function borrar($id)
    {
        $this->ConsultaSimple(
            "DELETE FROM equipos WHERE id = :id",
            array(":id" => $id)
        );
    }

    // Sube un puesto el equipo indicado (intercambia con el inmediatamente superior)
    public function subirPuesto($id)
    {
        // Obtener todos los equipos ordenados para conocer el índice real
        $this->ConsultaDatos("SELECT id, puesto FROM equipos ORDER BY puesto ASC");
        $lista = $this->filas;

        $idx = -1;
        for ($i = 0; $i < count($lista); $i++)
        {
            if ($lista[$i]["id"] == $id)
            {
                $idx = $i;
                break;
            }
        }

        // Si ya está primero o no se encontró, no se hace nada
        if ($idx <= 0) return;

        $puestoActual = $lista[$idx]["puesto"];
        $puestoArriba = $lista[$idx - 1]["puesto"];
        $idArriba     = $lista[$idx - 1]["id"];

        // Intercambiar los valores de puesto
        $this->ConsultaSimple(
            "UPDATE equipos SET puesto = :puesto WHERE id = :id",
            array(":puesto" => $puestoArriba, ":id" => $id)
        );
        $this->ConsultaSimple(
            "UPDATE equipos SET puesto = :puesto WHERE id = :id",
            array(":puesto" => $puestoActual, ":id" => $idArriba)
        );
    }

    // Baja un puesto el equipo indicado (intercambia con el inmediatamente inferior)
    public function bajarPuesto($id)
    {
        // Obtener todos los equipos ordenados para conocer el índice real
        $this->ConsultaDatos("SELECT id, puesto FROM equipos ORDER BY puesto ASC");
        $lista = $this->filas;
        $total = count($lista);

        $idx = -1;
        for ($i = 0; $i < $total; $i++)
        {
            if ($lista[$i]["id"] == $id)
            {
                $idx = $i;
                break;
            }
        }

        // Si ya está último o no se encontró, no se hace nada
        if ($idx < 0 || $idx >= $total - 1) return;

        $puestoActual = $lista[$idx]["puesto"];
        $puestoAbajo  = $lista[$idx + 1]["puesto"];
        $idAbajo      = $lista[$idx + 1]["id"];

        // Intercambiar los valores de puesto
        $this->ConsultaSimple(
            "UPDATE equipos SET puesto = :puesto WHERE id = :id",
            array(":puesto" => $puestoAbajo, ":id" => $id)
        );
        $this->ConsultaSimple(
            "UPDATE equipos SET puesto = :puesto WHERE id = :id",
            array(":puesto" => $puestoActual, ":id" => $idAbajo)
        );
    }
}

?>
