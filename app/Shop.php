<?php

namespace Merobot;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $guarded = [];
    protected $hidden = ['updated_at', 'created_at'];

    public function robots()
    {
        return $this->hasMany('Merobot\Robot');
    }
}
