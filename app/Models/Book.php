<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Categori;

class Book extends Model
{
    protected $fillable = [
        'title',
        'author',
        'publisher',
        'year',
        'price',
        'stock',
        'category_id'
    ];

    // Relasi Many-to-One ke Category
    public function category()
    {
        return $this->belongsTo(Categori::class);
    }
}
