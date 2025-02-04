<?php
declare(strict_types=1);
namespace Com\Daw2\Controllers;

use Com\Daw2\Core\BaseController;
use Com\Daw2\Libraries\Respuesta;
use Com\Daw2\Models\ProveedorModel;
use Google\Service\IdentityToolkit\VerifyAssertionResponse;

class ProveedoresController extends BaseController
{
    public function get(): void
    {
        $proveedorModel = new ProveedorModel();

        $condiciones = [];

        if (isset($_GET['cif'])) {
            $condiciones['cif'] = $_GET['cif'];
        }
        if (isset($_GET['pais'])) {
            $condiciones['pais'] = '%'.$_GET['pais'].'%';
        }
        if (isset($_GET['maximo_productos'])) {
            $condiciones['maximo_productos'] = $_GET['maximo_productos'];
        }
        if (isset($_GET['minimo_productos'])) {
            $condiciones['minimo_productos'] = $_GET['minimo_productos'];
        }

        if (!isset($_GET['order'])) {
            $order = 1;
        }
        if (isset($_GET['order'])) {
            $order = (int )$_GET['order'];
        }

        $totalElementos = $proveedorModel->countAllProveedores($condiciones);
        $paginas = $this->numeroPaginas($totalElementos);
        $pagina = $this->getPage($paginas);



        $proveedores = $proveedorModel->getAllProveedores($condiciones, $order, $pagina);
        $respuesta = new Respuesta(200, $proveedores);

        $this->view->show('json.view.php', ['respuesta' => $respuesta]);
    }

    public function numeroPaginas(int $numeroTotalElementos):int{
        return (int) ceil($numeroTotalElementos/$_ENV['limite.pagina']);
    }

    public function getPage(int $paginas):int
    {
        if(isset($_GET['page'])){
            if($_GET['page'] > 0 && $_GET['page'] <= $paginas){
                return (int) $_GET['page'];
            }
        }
        return 1;
    }


}