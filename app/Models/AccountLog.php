<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountLog extends Model
{
    protected $fillable = [
        'account_id',
        'account_log_type',
        'account_log_message',
        'account_log_change',
        'account_log_balance',
        'account_log_ref',
        'active',
    ];
    protected $boolean = [
        'active'
    ];
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
