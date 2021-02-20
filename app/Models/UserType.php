<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserType extends Model
{
    use HasFactory, SoftDeletes;

    const LOGIST = 1;
    const GENERAL = 2;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description'
    ];

    /**
     * Get the users for the Types.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
