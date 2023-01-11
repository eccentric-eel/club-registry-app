<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\Record;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        //clear any existing users/records
        User::truncate();
        Record::truncate();

        //create generic 'admin' user for testing
        $user = new User;
        $user->username = 'admin';
        $user->password = Hash::make('admin');
        $user->save();

        //populate with dummy data
        Record::factory(10000)->create();
    }
}
