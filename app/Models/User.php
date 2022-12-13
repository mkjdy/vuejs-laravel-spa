<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

//sanctum
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles, SoftDeletes;

    protected $guard_name = 'sanctum';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'username',
        'password',
        'is_active',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'name',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    public function getNameAttribute()
    {
        $middle = trim($this->attributes['middle_name']) ? ' '.(strlen($this->attributes['middle_name']) == 1 ? $this->attributes['middle_name'].'.' : $this->attributes['middle_name']).' ' : ' ';
        return $this->attributes['first_name'].$middle.$this->attributes['last_name'];
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];

    public function scopeWhereColumnContains($query, $searchTerm)
    {
        $attributes = [
            'first_name',
            'middle_name',
            'last_name',
            'username',
            'roles.name'
        ];

        $query->where(function ($query) use ($attributes, $searchTerm) {
            foreach ($attributes as $attribute) {
                $query->when(
                    str_contains($attribute, '.'),
                    function ($q) use ($attribute, $searchTerm) {
                        [$relationName, $relationAttribute] = explode('.', $attribute);

                        return $q->orWhereHas($relationName, function ($q) use ($relationAttribute, $searchTerm) {
                            $q->where($relationAttribute, 'LIKE', "%{$searchTerm}%");
                        });
                    },
                    function ($q) use ($attribute, $searchTerm) {
                        return $q->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                    }
                );
            }
        });

        return $query;
    }

}
