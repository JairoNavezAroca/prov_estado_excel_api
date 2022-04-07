<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int      $idProveedor
 * @property int      $idCargadoProveedores
 * @property int      $respuesta
 * @property int      $idOrigenProv
 * @property string   $ip
 * @property string   $token
 * @property string   $estadoRegistroDatos
 * @property string   $codigoRegistro
 * @property string   $ruc
 * @property string   $razon
 * @property string   $tipoEmpresa
 * @property string   $estado
 * @property string   $condicion
 * @property string   $departamento
 * @property string   $provincia
 * @property string   $distrito
 * @property string   $personeria
 * @property string   $codProv
 * @property string   $numRuc
 * @property string   $nomRzsProv
 * @property string   $lscIdTipReg
 * @property string   $lscIdTipRegVig
 * @property string   $cmcTexto
 * @property DateTime $fechaRegistroRuc
 * @property DateTime $fechaRegistroDatos
 * @property boolean  $process_
 * @property boolean  $esHabilitado
 * @property boolean  $esAptoContratar
 * @property int      $numeroIntentos
 */
class Proveedor extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'proveedor';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'idProveedor';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idCargadoProveedores', 'ip', 'token', 'fechaRegistroRuc', 'fechaRegistroDatos', 'estadoRegistroDatos', 'codigoRegistro', 'respuesta', 'ruc', 'razon', 'tipoEmpresa', 'estado', 'condicion', 'departamento', 'provincia', 'distrito', 'personeria', 'process_', 'codProv', 'idOrigenProv', 'numRuc', 'nomRzsProv', 'esHabilitado', 'lscIdTipReg', 'lscIdTipRegVig', 'esAptoContratar', 'cmcTexto', 'numeroIntentos'
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
        'idProveedor' => 'int', 'idCargadoProveedores' => 'int', 'ip' => 'string', 'token' => 'string', 'fechaRegistroRuc' => 'datetime', 'fechaRegistroDatos' => 'datetime', 'estadoRegistroDatos' => 'string', 'codigoRegistro' => 'string', 'respuesta' => 'int', 'ruc' => 'string', 'razon' => 'string', 'tipoEmpresa' => 'string', 'estado' => 'string', 'condicion' => 'string', 'departamento' => 'string', 'provincia' => 'string', 'distrito' => 'string', 'personeria' => 'string', 'process_' => 'boolean', 'codProv' => 'string', 'idOrigenProv' => 'int', 'numRuc' => 'string', 'nomRzsProv' => 'string', 'esHabilitado' => 'boolean', 'lscIdTipReg' => 'string', 'lscIdTipRegVig' => 'string', 'esAptoContratar' => 'boolean', 'cmcTexto' => 'string', 'numeroIntentos' => 'int'    
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'fechaRegistroRuc', 'fechaRegistroDatos'
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
