<?php

namespace Database\Seeders;

use App\Models\Categori;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['Fiksi', 'Non-Fiksi', 'Teknologi', 'Bisnis', 'Pendidikan'];

        foreach ($categories as $category) {
            Categori::create(['name' => $category]);
        }
    }
}
