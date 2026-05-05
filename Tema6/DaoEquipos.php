<?php

require_once 'DB.php';
require_once 'Equipo.php';

class DaoEquipos
{
    private $pdo;

    public function __construct()
    {
        $db = new DBProyecto();
        $this->pdo = $db->getConnection();
    }

    public function listar()
    {
        $sql = 'SELECT Id, Nombre, FechaFund, Presupuesto, Puesto, Logo FROM equipos ORDER BY Puesto ASC, Id ASC';
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll();

        $equipos = [];
        foreach ($rows as $row) {
            $equipos[] = new Equipo(
                (int)$row['Id'],
                $row['Nombre'],
                (int)$row['FechaFund'],
                (int)$row['Presupuesto'],
                (int)$row['Puesto'],
                $row['Logo']
            );
        }

        return $equipos;
    }

    public function totalEquipos()
    {
        $stmt = $this->pdo->query('SELECT COUNT(*) AS total FROM equipos');
        $row = $stmt->fetch();
        return (int)$row['total'];
    }

    public function insertar($nombre, $fechafund, $presupuesto, $logoBin)
    {
        $stmt = $this->pdo->query('SELECT COALESCE(MAX(Puesto), 0) + 1 AS next_puesto FROM equipos');
        $next = (int)$stmt->fetch()['next_puesto'];

        $sql = 'INSERT INTO equipos (Nombre, FechaFund, Presupuesto, Puesto, Logo) VALUES (:nombre, :fechafund, :presupuesto, :puesto, :logo)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindValue(':fechafund', mktime(0, 0, 0, 1, 1, $fechafund), PDO::PARAM_INT);
        $stmt->bindValue(':presupuesto', $presupuesto, PDO::PARAM_INT);
        $stmt->bindValue(':puesto', $next, PDO::PARAM_INT);
        $stmt->bindValue(':logo', $logoBin, PDO::PARAM_STR);

        $stmt->execute();
    }

    public function eliminar($id)
    {
        $stmt = $this->pdo->prepare('SELECT Puesto FROM equipos WHERE Id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return false;
        }

        $puestoBorrado = (int)$row['Puesto'];

        $this->pdo->beginTransaction();
        try {
            $del = $this->pdo->prepare('DELETE FROM equipos WHERE Id = :id');
            $del->execute([':id' => $id]);

            $shift = $this->pdo->prepare('UPDATE equipos SET Puesto = Puesto - 1 WHERE Puesto > :puesto');
            $shift->execute([':puesto' => $puestoBorrado]);

            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function actualizarPuesto($id, $nuevoPuesto)
    {
        $total = $this->totalEquipos();
        if ($total === 0) {
            return false;
        }

        if ($nuevoPuesto < 1) {
            $nuevoPuesto = 1;
        }
        if ($nuevoPuesto > $total) {
            $nuevoPuesto = $total;
        }

        $stmt = $this->pdo->prepare('SELECT Puesto FROM equipos WHERE Id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return false;
        }

        $puestoActual = (int)$row['Puesto'];
        if ($puestoActual === $nuevoPuesto) {
            return true;
        }

        $this->pdo->beginTransaction();
        try {
            if ($nuevoPuesto < $puestoActual) {
                $shift = $this->pdo->prepare(
                    'UPDATE equipos
                     SET Puesto = Puesto + 1
                     WHERE Puesto >= :nuevo AND Puesto < :actual AND Id <> :id'
                );
                $shift->execute([
                    ':nuevo' => $nuevoPuesto,
                    ':actual' => $puestoActual,
                    ':id' => $id
                ]);
            } else {
                $shift = $this->pdo->prepare(
                    'UPDATE equipos
                     SET Puesto = Puesto - 1
                     WHERE Puesto <= :nuevo AND Puesto > :actual AND Id <> :id'
                );
                $shift->execute([
                    ':nuevo' => $nuevoPuesto,
                    ':actual' => $puestoActual,
                    ':id' => $id
                ]);
            }

            $up = $this->pdo->prepare('UPDATE equipos SET Puesto = :puesto WHERE Id = :id');
            $up->execute([
                ':puesto' => $nuevoPuesto,
                ':id' => $id
            ]);

            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
