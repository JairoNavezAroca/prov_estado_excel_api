<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int      $idLogProveedores
 * @property int      $idProveedor
 * @property string   $ruc
 * @property string   $api_1
 * @property string   $api_2
 * @property DateTime $fecha_registro
 */
class LogProveedores extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'log_proveedores';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'idLogProveedores';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idProveedor', 'ruc', 'fecha_registro', 'api_1', 'api_2'
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
        'idLogProveedores' => 'int', 'idProveedor' => 'int', 'ruc' => 'string', 'fecha_registro' => 'datetime', 'api_1' => 'string', 'api_2' => 'string'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'fecha_registro'
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
