<?php

namespace App\Models\Home;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'openid', 'session_key', 'name', 'phone', 'max_score',
        'min_score', 'province_id', 'subject', 'times', 'is_vip',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'openid', 'session_key',
    ];

    public static function add($data) {
        $user = self::updateOrCreate(['openid' => $data['openid']], $data);
        return $user;
    }

    public static function setInfo($uid, $data) {
        $user = self::find($uid);
        $data['times'] = $user->times + 1;
        $user->update($data);
        return $user;
    }
}
