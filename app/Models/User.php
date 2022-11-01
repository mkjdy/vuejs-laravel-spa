<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

//sanctum
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function scopeWhereColumnContains($query, $searchTerm)
    {
        $attributes = [
            'name',
            // 'username',
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
