<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{    
    protected $table = 'user_details';
    protected $fillable = ['profession', 'bio', 'age', 'experience', 'state', 'country'];
}
