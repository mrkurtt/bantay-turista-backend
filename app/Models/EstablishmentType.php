<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstablishmentType extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function establishments()
    {
        return $this->hasMany(Establishment::class);
    }

}