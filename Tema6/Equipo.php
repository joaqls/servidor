<?php

class Equipo
{
    public $id;
    public $nombre;
    public $fechafund;
    public $presupuesto;
    public $puesto;
    public $logo;

    public function __construct($id = null, $nombre = '', $fechafund = 0, $presupuesto = 0, $puesto = 0, $logo = null)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->fechafund = $fechafund;
        $this->presupuesto = $presupuesto;
        $this->puesto = $puesto;
        $this->logo = $logo;
    }
}
