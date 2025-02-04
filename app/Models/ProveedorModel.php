<?php
declare(strict_types=1);
namespace Com\Daw2\Models;

use Com\Daw2\Core\BaseDbModel;

class ProveedorModel extends BaseDbModel
{
    CONST COLUMNAS_ORDER = ['p.cif','p.pais', 'numero_productos'];

    public function getFiltros( array $condiciones): array
    {
        $filtrosSQL = [];
        $filtroHAVING = [];
        if (isset($condiciones['cif'])) {
            $filtrosSQL['cif'] = 'p.cif LIKE :cif';
        }
        if (isset($condiciones['pais'])) {
            $filtrosSQL['pais'] = 'p.pais LIKE :pais';
        }
        return $filtrosSQL;
    }

    public function having(array $condiciones){
        $filtroHAVING = [];

        if (isset($condiciones['maximo_productos'])) {
            $filtroHAVING['maximo_productos'] = 'numero_productos <= :maximo_productos';
        }
        if (isset($condiciones['minimo_productos'])) {
            $filtroHAVING['minimo_productos'] = 'numero_productos >= :minimo_productos';
        }

        return $filtroHAVING;
    }

    public function getAllProveedores( array $condiciones, int $order, int $page): array
    {
        $limite = ($page -1)*$_ENV['limite.pagina'];
        $tamanoPagina = $_ENV['limite.pagina'];

        $condicionesSQL = $this->getFiltros($condiciones);
        $condicionesHaving = $this->having($condiciones);
        $sentidoOrder = ($order > 0) ? 'ASC' : 'DESC';
        $order = abs($order)-1;

        $sql = "SELECT p.cif, p.pais, COUNT(DISTINCT(p2.codigo)) AS numero_productos
                FROM proveedor p
                LEFT JOIN producto p2 ON p2.proveedor = p.cif ";
        if (!empty($condicionesSQL)) {
            $sql .= " WHERE " . implode(" AND ", $condicionesSQL);
        }
        $sql .= " GROUP BY p.cif ";
        if (!empty($condicionesHaving)) {
            $sql .= " HAVING " . implode(" AND ", $condicionesHaving);
        }
        $sql.= " ORDER BY " . self::COLUMNAS_ORDER[$order]." ".$sentidoOrder;
        $sql.= " LIMIT ".$limite.', '.$tamanoPagina;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($condiciones);
        return $stmt->fetchAll();
    }

    public function countAllProveedores( array $condiciones): int
    {
        $sql = "SELECT COUNT(DISTINCT(cif)) AS numero_productos"
            . " FROM proveedor";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($condiciones);
        return (int) $stmt->fetchColumn();
    }
}