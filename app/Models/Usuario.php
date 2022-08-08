<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int     $idUsuario
 * @property int     $idEstadoUsuario
 * @property string  $nombres
 * @property string  $apellidoPaterno
 * @property string  $apellidoMaterno
 * @property string  $contrasena
 * @property string  $contrasenaInicial
 * @property boolean $flagObligarCambiarContrasena
 */
class Usuario extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'mysql';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'usuario';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'idUsuario';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombres', 'apellidoPaterno', 'apellidoMaterno', 'flagObligarCambiarContrasena', 'idEstadoUsuario'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'contrasena', 'contrasenaInicial'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'idUsuario' => 'int', 'nombres' => 'string', 'apellidoPaterno' => 'string', 'apellidoMaterno' => 'string', 'contrasena' => 'string', 'contrasenaInicial' => 'string', 'flagObligarCambiarContrasena' => 'boolean', 'idEstadoUsuario' => 'int'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = false;

    // Scopes...

    // Functions ...

    // Relations ...
}
