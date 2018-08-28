<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'account_no',
        'account_name',
        'account_type',
        'account_autosync',
        'account_balance',
        'account_options',
        'active',
    ];
    protected $boolean = [
        'account_autosync',
        'active'
    ];
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
