<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait FiltrarPorLocatario
{
    protected static function bootFiltrarPorLocatario()
    {
        // Global Scope para filtrar por locatário automaticamente
        static::addGlobalScope('locatario', function (Builder $builder) {
            if (session()->has('locatario_atual')) {
                $locatario = \App\Models\Locatario::where('slug', session('locatario_atual'))->first();
                if ($locatario && !$builder->getQuery()->columns) {
                    $builder->where($builder->getModel()->getTable() . '.locatario_id', $locatario->id);
                }
            }
        });

        // Evento que adiciona o locatario_id automaticamente ao criar
        static::creating(function ($model) {
            if (session()->has('locatario_atual')) {
                $locatario = \App\Models\Locatario::where('slug', session('locatario_atual'))->first();
                if ($locatario && !$model->locatario_id) {
                    $model->locatario_id = $locatario->id;
                }
            }
        });
    }
}
