<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $idProveedorRepresentante
 * @property int    $idProveedor
 * @property int    $idRepresentante
 * @property int    $idCargo
 * @property string $codigoRegistro
 * @property string $codigoDocIde
 * @property string $descDocIde
 * @property string $siglaDocIde
 * @property string $nroDocumento
 * @property string $razonSocial
 * @property string $descCargo
 * @property string $numeroRuc
 * @property Date   $fechaIngreso
 */
class ProveedorRepresentante extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'proveedor_representante';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'idProveedorRepresentante';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idProveedor', 'idRepresentante', 'codigoRegistro', 'codigoDocIde', 'descDocIde', 'siglaDocIde', 'nroDocumento', 'razonSocial', 'idCargo', 'descCargo', 'numeroRuc', 'fechaIngreso'
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
        'idProveedorRepresentante' => 'int', 'idProveedor' => 'int', 'idRepresentante' => 'int', 'codigoRegistro' => 'string', 'codigoDocIde' => 'string', 'descDocIde' => 'string', 'siglaDocIde' => 'string', 'nroDocumento' => 'string', 'razonSocial' => 'string', 'idCargo' => 'int', 'descCargo' => 'string', 'numeroRuc' => 'string', 'fechaIngreso' => 'date'
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
