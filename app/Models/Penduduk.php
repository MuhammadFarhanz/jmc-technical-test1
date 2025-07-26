<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penduduk extends Model
{
    protected $fillable = [
        'nama',
        'nik',
        'umur',
        'alamat',
        'kabupaten_id',
        'provinsi_id',
    ];

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class);
    }

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class);
    }
}
