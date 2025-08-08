<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MajidUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'مجید رستمیان',
            'mobile' => '09169215300',
            'email' => 'pofelak@gmail.com',
            'mobile_verified_at' =>now(),
            'type' => 'admin',
            'password' => \Hash::make('Dg#1p54W)ax^52'),

        ]);
    }
}
