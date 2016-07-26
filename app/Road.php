<?php

namespace Merobot;

use Illuminate\Database\Eloquent\Model;

class Road extends Model
{
    protected $guarded = [];
    protected $hidden = ['updated_at', 'created_at'];
}
