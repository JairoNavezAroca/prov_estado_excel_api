<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $idProveedorSocio
 * @property int    $idProveedor
 * @property int    $idSocio
 * @property string $codigoRegistro
 * @property string $codigoDocIde
 * @property string $descDocIde
 * @property string $siglaDocIde
 * @property string $nroDocumento
 * @property string $numeroAcciones_
 * @property string $porcentajeAcciones_
 * @property string $razonSocial
 * @property string $numeroRuc
 * @property Date   $fechaIngreso
 */
class ProveedorSocio extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'proveedor_socio';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'idProveedorSocio';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idProveedor', 'idSocio', 'codigoRegistro', 'codigoDocIde', 'descDocIde', 'siglaDocIde', 'nroDocumento', 'numeroAcciones_', 'numeroAcciones', 'porcentajeAcciones_', 'porcentajeAcciones', 'razonSocial', 'numeroRuc', 'fechaIngreso'
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
        'idProveedorSocio' => 'int', 'idProveedor' => 'int', 'idSocio' => 'int', 'codigoRegistro' => 'string', 'codigoDocIde' => 'string', 'descDocIde' => 'string', 'siglaDocIde' => 'string', 'nroDocumento' => 'string', 'numeroAcciones_' => 'string', 'porcentajeAcciones_' => 'string', 'razonSocial' => 'string', 'numeroRuc' => 'string', 'fechaIngreso' => 'date'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'fechaIngreso'
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
