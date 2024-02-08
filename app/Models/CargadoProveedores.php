<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int      $idCargadoProveedores
 * @property string   $ip
 * @property string   $token
 * @property DateTime $fechaImportacion
 * @property DateTime $fechaExportacion
 */
class CargadoProveedores extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cargado_proveedores';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'idCargadoProveedores';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ip', 'token', 'fechaImportacion', 'fechaExportacion', 'flagBusquedaCompleta'
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
        'idCargadoProveedores' => 'int',
        'ip' => 'string',
        'token' => 'string',
        'fechaImportacion' => 'datetime',
        'fechaExportacion' => 'datetime',
        'flagBusquedaCompleta' => 'boolean'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'fechaImportacion', 'fechaExportacion'
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
