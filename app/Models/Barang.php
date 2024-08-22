<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;
    protected $fillable = [
        'nama_barang',
        'kode',
        'kategori',
        'lokasi',
        'stok',
    ];
    public function mutasis()
    {
        return $this->hasMany(Mutasi::class);
    }
}
