<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categori extends Model
{
    protected $table = 'categoris'; // Perhatikan nama tabel

    protected $fillable = ['name'];

    public function books()
    {
        return $this->hasMany(Book::class, 'category_id');
    }
}
