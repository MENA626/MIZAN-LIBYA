<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        
        // 🛠️ تسجيل الحراس المخصصين (Alias) لسهولة استخدامهم في routes/web.php
        $middleware->alias([
            'verified.lawyer' => \App\Http\Middleware\EnsureLawyerIsVerified::class,
            'force.single'    => \App\Http\Middleware\ForceSingleUser::class,
            'role'            => \App\Http\Middleware\EnsureUserRole::class,
        ]);

        // توجيه الضيوف (غير المسجلين) دائماً إلى صفحة تسجيل الدخول
        $middleware->redirectGuestsTo(fn () => route('login'));

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // معالجة الأخطاء الأمنية مثل انتهاء الجلسة (419)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, Request $request) {
            if ($e->getStatusCode() === 419) {
                return redirect()->route('login')->with('error', 'انتهت صلاحية الجلسة، يرجى تسجيل الدخول مرة أخرى.');
            }
        });
    })->create();