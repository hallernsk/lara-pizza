<?php

use App\Http\Middleware\AdminMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
        $middleware->alias([  //
            'admin' => AdminMiddleware::class, // регистрация middleware
          ]); //

        //   $middleware->validateCsrfTokens(except: [ 
        //     '*',
            // 'admin/products/*',  // маршруты, связанные с товарами в админке
            // 'cart/*',          
            // 'orders',
            // 'orders/*'          
            //   ]); 

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
