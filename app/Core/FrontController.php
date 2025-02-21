<?php

namespace Com\Daw2\Core;

use Com\Daw2\Controllers\ProveedoresController;
use Com\Daw2\Controllers\UsuarioSistemaController;
use Ahc\Jwt\JWT;
use Steampixel\Route;
use Com\Daw2\Helpers\JwtTool;


class FrontController
{
    private static ?array $jwtData = null;
    private static array $permisos = [];

    public static function main()
    {
        if (JwtTool::requestHasToken()){
            $bearer = JwtTool::getBearerToken();
            $jwt = new JWT($_ENV['secret']);
            self::$jwtData = $jwt->decode($bearer);
            self::$permisos = UsuarioSistemaController::getPermisos(self::$jwtData['id_rol']);
        }else{
            self::$permisos = UsuarioSistemaController::getPermisos();
        }

        Route::add(
            '/login',
            fn() => (new UsuarioSistemaController())->login(),
            'post'
        );

        Route::add(
            '/proveedor',
            function () {
                if (str_contains(self::$permisos['proveedores'],'r')){
                    (new ProveedoresController())->get();
                }else{
                    http_response_code(403);
                }
            },
            'get'
        );

        Route::add(
            '/proveedor/([A-Z][0-9]{0,7}[A-Z])',
           function ($id) {
             if (str_contains(self::$permisos['proveedores'],'r')){
                 (new ProveedoresController())->getbyId($id);
             }else{
                 http_response_code(403);
             }
           },
            'get'
        );

        Route::add(
            '/proveedor',
            function () {
              if (str_contains(self::$permisos['proveedores'],'w')){
                  (new ProveedoresController())->post();
              }else{
                  http_response_code(403);
              }
            },
            'post'
        );

        Route::add(
            '/proveedor/([A-Z][0-9]{7}[A-Z])',
            function ($id) {
                if (str_contains(self::$permisos['proveedores'],'w')){
                    $controller = new ProveedoresController();
                    $controller->delte((int) $id);
                }else{
                    http_response_code(403);
                }
            },
            'delete'
        );

        Route::pathNotFound(
            function () {

            }
        );

        Route::methodNotAllowed(
            function () {

            }
        );
        
        Route::run();
    }
}
