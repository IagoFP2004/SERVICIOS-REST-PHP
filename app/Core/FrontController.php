<?php

namespace Com\Daw2\Core;

use Com\Daw2\Controllers\ProveedoresController;
use Steampixel\Route;

class FrontController
{
    public static function main()
    {
        Route::add(
            '/proveedor',
            fn() => (new ProveedoresController())->get(),
            'get'
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
