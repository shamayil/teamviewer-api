<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RemoteLog extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tblcustomerremotelog';
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'rno';
    /**
     * * Indicates model access colmuns.
     *
     * @var bool
     */

    protected $fillable = [
        'custno', 'userid', 'connecttime', 'remotecontrol_id', 'issuerno', 'comment', 'deviceid', 'devicename',
        'groupid', 'start_date', 'end_date'
    ];

}
