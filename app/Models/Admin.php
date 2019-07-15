<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $guard = 'admin';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function update_rules()
    {

        return [
            'first_name'       => 'required|max:100|min:3',
            'contact_number'   => 'required|max:20|min:3|unique:admins,contact_number,'.Auth::id(),
            'email'            => 'nullable|email|max:100|min:3|unique_with:admins,email,'.Auth::id(),
        ];
    }

    public static function change_password()
    {
        return [
            'old_password'      => 'required|max:20|min:3',
            'new_password'      => 'required|max:20|min:3',
            'confirm_password'  => 'required|max:20|min:3|same:new_password',
        ];
    }

    public function getSettingAttribute($value)
    {
        return (object)json_decode($value);
    }

    public function setSettingAttribute($value)
    {
        $this->attributes['setting'] = json_encode($value);
    }
}
