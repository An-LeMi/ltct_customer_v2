<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create admin user
        $user = new User();
        $user->username = 'admin';
        $user->password = Hash::make('123456');
        $user->email = 'admin@gmail.com';
        $user->phone = '0123456789';
        $user->role = 'admin';
        $user->status = 'active';
        $user->save();
        // create 10 customer users
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->username = 'customer' . $i;
            $user->password = Hash::make('123456');
            $user->email = 'customer' . $i . '@gmail.com';
            $user->phone = '01234567' . $i;
            $user->role = 'customer';
            $user->status = 'active';
            $user->save();
        }
    }
}
