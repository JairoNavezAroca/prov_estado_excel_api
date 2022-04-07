<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $idProveedorOrganoAdministrativo
 * @property int    $idProveedor
 * @property int    $idOrgano
 * @property int    $idTipoOrgano
 * @property int    $idCargo
 * @property string $codigoRegistro
 * @property string $codigoDocIde
 * @property string $descDocIde
 * @property string $siglaDocIde
 * @property string $nroDocumento
 * @property string $apellidosNomb
 * @property string $descTipoOrgano
 * @property string $descCargo
 * @property Date   $fechaIngreso
 */
class ProveedorOrganoAdministrativo extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'proveedor_organo_administrativo';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'idProveedorOrganoAdministrativo';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idProveedor', 'idOrgano', 'codigoRegistro', 'codigoDocIde', 'descDocIde', 'siglaDocIde', 'nroDocumento', 'apellidosNomb', 'idTipoOrgano', 'descTipoOrgano', 'idCargo', 'descCargo', 'fechaIngreso'
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
        'idProveedorOrganoAdministrativo' => 'int', 'idProveedor' => 'int', 'idOrgano' => 'int', 'codigoRegistro' => 'string', 'codigoDocIde' => 'string', 'descDocIde' => 'string', 'siglaDocIde' => 'string', 'nroDocumento' => 'string', 'apellidosNomb' => 'string', 'idTipoOrgano' => 'int', 'descTipoOrgano' => 'string', 'idCargo' => 'int', 'descCargo' => 'string', 'fechaIngreso' => 'date'
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
