<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use Carbon\Carbon;

class AdminUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::query()->updateOrCreate([
           'email' => 'admin@aroundairports.com'
        ], [
            'name' => 'Admin',
            'email' => 'admin@aroundairports.com',
            'password' => bcrypt('password'),
            'email_verified_at' => Carbon::now()
        ]);
    }
}
