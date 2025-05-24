<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    use SoftDeletes;

    protected $table = 'tables';

    protected $fillable = [
        'nama_meja',
        'qr_code',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];
    
    
    public function sales()
    {
        return $this->hasMany(Sale::class, 'id_meja');
    }
}
