<?php

namespace App\Models;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
//use Illuminate\Foundation\Auth as Authenticatable;

class Ati_sausers
{
     use HasApiTokens, Notifiable;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'SAUSERS_ID','AUSER_NAME', 'UPASSWORDS',  
            ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'UPASSWORDS', 'remember_token',
    ];
}
