<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;


class Client extends Authenticatable
{
    protected $table = 'clients';
    protected $primaryKey = 'CLIENT_ID';

    protected $fillable = [
        'CLIENT_NAME',
        'BUSINESS_TYPE',
        'EMAIL',
        'password',
        'PHONE'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
