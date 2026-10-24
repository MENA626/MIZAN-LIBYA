<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB; // استدعاء كلاس قاعدة البيانات المباشر
use Illuminate\Support\Facades\View; // استدعاء كلاس الفيو
use Illuminate\Support\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // تمرير التخصصات مباشرة من الجدول لمنع خطأ الموديل المفقود
        View::composer('lawyer.settings', function ($view) {
            $specialties = DB::table('law_categories')->get();
            $view->with('allSpecialties', $specialties);
            Carbon::setLocale('ar'); // هذا سيجعل كل التواريخ بالعربية
        });
    }
}