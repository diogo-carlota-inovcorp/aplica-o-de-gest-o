<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LocatarioServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Filtrar automaticamente queries pelo locatário atual
        if (session()->has('locatario_atual')) {
            DB::statement("SET @locatario_id = ?", [session('locatario_atual_id')]);
        }
    }

    public function register()
    {
        //
    }
}
