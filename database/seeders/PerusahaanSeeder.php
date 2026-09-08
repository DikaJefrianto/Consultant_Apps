<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Perusahaan;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class PerusahaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        for ($i = 0; $i < 50; $i++) {
            // Buat user perusahaan
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'username' => $faker->unique()->userName,
                'password' => Hash::make('password123'), // password default
            ]);

            // Assign role 'perusahaan' ke user
            // $user->assignRole('perusahaan');

            // Buat perusahaan dan kaitkan dengan user_id
            Perusahaan::create([
                'user_id' => $user->id,
                'nama' => $faker->company,
                'email' => $faker->unique()->companyEmail,
                'alamat' => $faker->address,
                'created_at' => Carbon::now()->subDays(rand(1, 365)),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
