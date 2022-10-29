<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SuperAdmin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'login_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'username',
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'login_at' => 'datetime',
    ];

    public function setImageURL()
    {
        if(isset($this->image)){
            $image = Storage::get('profile_picture/super_admin/' . $this->image);
            $type = pathinfo('profile_picture/super_admin/' . $this->image, PATHINFO_EXTENSION);
            $this->image = 'data:image/' . $type . ';base64,' . base64_encode($image);
        }
        else{
            $this->image = 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png';
        }
    }

    public function getImageURL()
    {
        if(isset($this->image)){
            $image = Storage::get('profile_picture/super_admin/' . $this->image);
            $type = pathinfo('profile_picture/super_admin/' . $this->image, PATHINFO_EXTENSION);
            return 'data:image/' . $type . ';base64,' . base64_encode($image);
        }

        return 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png';
    }
}
