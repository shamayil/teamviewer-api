<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerRemote extends Model
{
    /**
     * Indicates model access colmuns.
     *
     * @var bool
     */

    public $timestamps = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tblcustomerremote';
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
        'custno', 'remotecontrol_id', 'device_id', 'alias', 'groupid', 'online_state', 'policy_id', 'assigned_to',
        'last_seen', 'posremoteid', 'description', 'supported_features', 'status', 'comment', 'password', 'update_time',
    ];

}
