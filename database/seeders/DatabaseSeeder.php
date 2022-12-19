<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Record;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        Record::truncate();

        Record::factory(100)->create();
    }
}
