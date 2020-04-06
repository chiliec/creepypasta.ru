<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Cog\Contracts\Love\Reacterable\Models\Reacterable as ReacterableContract;
use Cog\Laravel\Love\Reacterable\Models\Traits\Reacterable;
use Soved\Laravel\Magic\Auth\Contracts\CanMagicallyLogin as CanMagicallyLoginContract;
use Soved\Laravel\Magic\Auth\Traits\CanMagicallyLogin;

class User extends Authenticatable implements ReacterableContract, CanMagicallyLoginContract
{
    use Notifiable;
    use Reacterable;
    use CanMagicallyLogin;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * A user can have many posts
     *
     * @return HasMany
     */
    public function posts()
    {
        return $this->hasMany('App\Post', 'user_id');
    }
}
