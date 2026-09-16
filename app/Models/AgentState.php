<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentState extends Model
{
    protected $fillable = [
        'session_id',
        'selected_product_id',
    ];
}
