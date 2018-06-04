<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
//use Zizaco\Entrust\Traits\EntrustUserTrait;



class User extends Authenticatable
{
   //  use EntrustUserTrait;
    
     protected $table = 'SAV_SAUSERS';
     protected $primaryKey = 'ID';
  
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    
    protected $fillable = [
                            'SAUSERS_ID','USRS_FNAME','DESGTON_ID',
                            'DEPRTMN_ID','EMPLOYE_ID','AUSER_NAME',
                            'UPASSWORDS','USER_PHOTO','USER_TYPES',
                            'USERMOBILE','USER_EMAIL','ADMINUSRFG',
                            'MNLAGRP_ID','CLIENTS_ID','ACPINFO_ID',
                            'BASELNK_ID','COMPANY_ID','CBRANCH_ID',
                            'COBUNIT_ID','PTGUNIT_ID'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
     // Override required, otherwise existing Authentication system will not match credentials
    public function getAuthPassword()
    {
        return $this->UPASSWORDS;
    }
    protected $hidden = [
        'UPASSWORDS', 'REMEMBER_TOKEN',
    ];
}
