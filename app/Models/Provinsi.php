<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    protected $fillable = [
        'nama',
        // Add other fields as needed
    ];

    public function kabupatens()
    {
        return $this->hasMany(Kabupaten::class);
    }
}