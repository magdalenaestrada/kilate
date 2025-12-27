<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Abonado extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'abonados';

    protected $dates = ['deleted_at'];

    public function ranchos()
    {
        return $this->belongsToMany(Rancho::class, 'abonado_rancho')->withTimestamps();
    }
}
