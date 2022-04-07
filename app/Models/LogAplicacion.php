<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int      $idLogAplicacion
 * @property string   $metodo
 * @property string   $ruta
 * @property string   $parametros_entrada
 * @property string   $ip_usuario
 * @property string   $comentario
 * @property string   $tipo
 * @property string   $detalle
 * @property DateTime $fechahora
 */
class LogAplicacion extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'log_aplicacion';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'idLogAplicacion';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'metodo', 'ruta', 'parametros_entrada', 'ip_usuario', 'comentario', 'tipo', 'detalle', 'fechahora'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'idLogAplicacion' => 'int', 'metodo' => 'string', 'ruta' => 'string', 'parametros_entrada' => 'string', 'ip_usuario' => 'string', 'comentario' => 'string', 'tipo' => 'string', 'detalle' => 'string', 'fechahora' => 'datetime'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'fechahora'
    ];

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
