<?php
declare(strict_types=1);
namespace Com\Daw2\Controllers;

use Com\Daw2\Core\BaseController;
use Com\Daw2\Libraries\Respuesta;
use Com\Daw2\Models\UsuariosSistemaModel;
use Ahc\Jwt\JWT;
use Google\Service\IdentityToolkit\VerifyAssertionResponse;
use function GuzzleHttp\default_user_agent;

class UsuarioSistemaController extends BaseController
{
    private const ROL_ADMIN = 1;
    private const ROL_ENCARGADO = 2;
    private const ROL_STAFF = 3;
    public function login():void
    {
        if(empty($_POST['email']) || empty($_POST['password'])){
            $respuesta = new Respuesta(400);
        }else{
            $modelo = new UsuariosSistemaModel();
            $login = $modelo->login($_POST['email']);

            if($login!==false){

                if (password_verify($_POST['password'], $login['pass'])) {
                    $paiload = [
                        'id_rol' => $login['id_rol'],
                        'email' => $login['email'],
                        'nombre' => $login['nombre'],
                    ];
                    $jwt = new JWT('Hx{seL_Iwb@urI£PYb.mW3]dv0M01KRl.2wYtl"__mV9', 'HS256', 1800);
                    $token = $jwt->encode($paiload);
                    $respuesta = new Respuesta(200, ['token' => $token]);
                }else{
                    $respuesta = new Respuesta(403, ['mensaje'=>'Datos incorrectos']);
                }
            }

        }
    $this->view->show('json.view.php', ['respuesta'=>$respuesta]);
    }

    public static function getPermisos(int $idRol = -1):array
    {
        $permisos = [
            'proveedores'=>''
        ];
        return match ($idRol) {
            self::ROL_ADMIN => array_replace_recursive($permisos,['proveedores'=>'rwd']),
            self::ROL_ENCARGADO => array_replace_recursive($permisos,['proveedores'=>'r']),
            self::ROL_STAFF => array_replace_recursive($permisos,[]),
            default=>$permisos
        };
    }
}