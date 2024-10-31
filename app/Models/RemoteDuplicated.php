<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RemoteDuplicated extends Model
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
    protected $table = 'tblremoteduplicate';
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'rno';
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

}
