<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'policy_number',
        'expiration_date',
        'deleted_at',
        'last_sent_at',
        'last_message_id',
        'last_message_status',
        'last_message_auto_replied_at'
    ];
}
