<?php

namespace Merobot;

use Illuminate\Database\Eloquent\Model;

class Robot extends Model
{
    protected $guarded = [];
    protected $hidden = ['updated_at', 'created_at'];

    public function roads()
    {
        return $this->hasMany('Merobot\Road');
    }
}