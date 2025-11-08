<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TodoList;

class TodoListSeeder extends Seeder
{
    public function run(): void
    {
        TodoList::insert([
            [
                'title'    => 'Belajar Laravel',
                'desc'     => 'Belajar Laravel 12',
                'is_done'  => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title'    => 'Belajar Node Js',
                'desc'     => 'Belajar Node Js 18',
                'is_done'  => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
