<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. التأكد من أن المستخدم مسجل دخول
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'يجب عليك تسجيل الدخول أولاً.');
        }

        // 2. التحقق من الصلاحية
        if (!in_array(Auth::user()->role, $roles)) {
            
            // بدلاً من تسجيل الخروج (Logout)، نقوم فقط بإرجاعه للخلف أو لصفحة الصلاحيات
            // تسجيل الخروج هنا قد يطرده من النظام كاملاً وهو ما زال مسجل دخول كعميل!
            
            return redirect()->back()->with('error', 'عذراً، ليس لديك صلاحية الوصول لهذه الصفحة.');
        }

        return $next($request);
    }
}
