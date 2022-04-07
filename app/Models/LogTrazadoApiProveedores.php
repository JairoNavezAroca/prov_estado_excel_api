<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int   $idTrazadoApiProveedores
 * @property int   $idProveedor
 * @property float $consulta_token_ruc
 * @property float $peticion_api_1
 * @property float $peticion_api_2
 * @property float $conversion
 * @property float $total
 */
class LogTrazadoApiProveedores extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'log_trazado_api_proveedores';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'idTrazadoApiProveedores';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idProveedor', 'consulta_token_ruc', 'peticion_api_1', 'peticion_api_2', 'conversion', 'registro_bd', 'total'
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
        'idTrazadoApiProveedores' => 'int', 'idProveedor' => 'int','consulta_token_ruc' => 'double', 'peticion_api_1' => 'double', 'peticion_api_2' => 'double', 'conversion' => 'double', 'registro_bd' => 'double', 'total' => 'double'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        
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
