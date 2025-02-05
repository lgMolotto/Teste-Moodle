<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class DisciplinaModel extends Authenticatable
{
    use SoftDeletes;
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'disciplina';
    protected $primaryKey = 'codigo_disciplina';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'codigo_professor',
        'nome',
        'descricao',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid_disciplina = (string) Str::uuid();
            $model->created_by = Auth::user()?->codigo_usuario;
        });

        static::updating(function ($model) {
            $model->edited_by = Auth::user()?->codigo_usuario;
        });

        static::deleting(function ($model) {
            $model->deleted_by = Auth::user()?->codigo_usuario;
            $model->save(); // necessário para garantir que o valor seja persistido antes do soft delete
        });
    }

    public function professor()
    {
        return $this->belongsTo(ProfessorModel::class, 'codigo_professor');
    }
}
