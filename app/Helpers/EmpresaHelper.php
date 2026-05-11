<?php

namespace App\Helpers;

use App\Models\EmpresaConfig;

class EmpresaHelper
{
    public static function getConfig()
    {
        return EmpresaConfig::getConfig();
    }

    public static function getLogoUrl()
    {
        $config = self::getConfig();
        if ($config->logo) {
            return asset('storage/' . $config->logo);
        }
        return null;
    }

    public static function getNome()
    {
        return self::getConfig()->nome;
    }

    public static function getMoradaCompleta()
    {
        $config = self::getConfig();
        $morada = [];

        if ($config->morada) $morada[] = $config->morada;
        if ($config->codigo_postal) $morada[] = $config->codigo_postal;
        if ($config->localidade) $morada[] = $config->localidade;

        return implode(', ', $morada);
    }
}
