<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\AuditTrail;


class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function audit_trail(){

        return $this->hasMany('App\AuditTrail');

    }

    public function log($message){

        
        $message = ucwords($message);
        
         $data = [
                'user_id' => $this->id,
                'name' => $this->name,
                'date' => Carbon::parse(now())-toDateString(),
                'activity' => "{$this->name} $message"
         ];
         AuditTrail::query()->create($data);

    }
}
