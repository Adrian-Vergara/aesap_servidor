<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use App\User;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();
        DB::table('users')->delete();

        $users = array(
            ['name' => 'Adrian', 'email' => 'adrian@gmail.com', 'password' => Hash::make('secret')],
            ['name' => 'Neymar', 'email' => 'neymar@gmail.com', 'password' => Hash::make('secret')],
            ['name' => 'Messi', 'email' => 'messi@gmail.com', 'password' => Hash::make('secret')],
            ['name' => 'Luis', 'email' => 'luis@gmail.com', 'password' => Hash::make('secret')]
        );

        foreach($users as $user)
        {
            User::create($user);
        }

        Model::reguard();
    }
}
