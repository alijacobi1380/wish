<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Replays extends Model
{
    use HasFactory;

    function ordr($query)
    {
        return $query->orderBy('id', 'DESC');
    }
}
