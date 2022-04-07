<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int      $idProveedorAntecedente
 * @property int      $idProveedor
 * @property string   $sanciones
 * @property string   $inhsJudicial
 * @property string   $inhsAdministrativa
 * @property DateTime $fechaConsultaSancTCE
 * @property DateTime $fechaConsultaInhabAD
 * @property DateTime $fechaConsultaInhabMJ
 * @property boolean  $process_
 */
class ProveedorAntecedente extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'proveedor_antecedente';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'idProveedorAntecedente';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idProveedor', 'sanciones', 'inhsJudicial', 'inhsAdministrativa', 'fechaConsultaSancTCE', 'fechaConsultaInhabAD', 'fechaConsultaInhabMJ', 'process_'
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
        'idProveedorAntecedente' => 'int', 'idProveedor' => 'int', 'sanciones' => 'string', 'inhsJudicial' => 'string', 'inhsAdministrativa' => 'string', 'fechaConsultaSancTCE' => 'datetime', 'fechaConsultaInhabAD' => 'datetime', 'fechaConsultaInhabMJ' => 'datetime', 'process_' => 'boolean'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'fechaConsultaSancTCE', 'fechaConsultaInhabAD', 'fechaConsultaInhabMJ'
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
