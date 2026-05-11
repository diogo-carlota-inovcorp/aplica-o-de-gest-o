<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'telemovel',
        'grupo_permissao_id',
        'estado',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'estado' => 'boolean',
        ];
    }

    public function grupoPermissao()
    {
        return $this->belongsTo(GrupoPermissao::class, 'grupo_permissao_id');
    }

     public function getActivitylogOptions(): LogOptions
    {
      return LogOptions::defaults()
          ->logOnly(['name', 'email', 'grupo_permissao_id', 'estado'])
           ->logOnlyDirty()
       ->dontSubmitEmptyLogs();
 }


    public function locatarios()
    {
        return $this->belongsToMany(Locatario::class, 'locatario_user')
            ->withPivot('funcao', 'permissoes', 'ativo', 'convidado_em', 'aceitou_em')
            ->withTimestamps();
    }


    public function locatarioAtual()
    {
        if (session()->has('locatario_atual')) {
            return $this->locatarios()->where('slug', session('locatario_atual'))->first();
        }

        return $this->locatarios()->first();
    }

   
    public function isProprietarioDe(Locatario $locatario)
    {
        $pivot = $this->locatarios()->where('locatario_id', $locatario->id)->first();
        return $pivot && $pivot->pivot->funcao === 'proprietario';
    }
}
