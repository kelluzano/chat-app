<?php

namespace Database\Seeders;

use App\Models\Session;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
class SessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        for($x = 1; $x <= 20; $x++){
            $data = Session::create([
                'uniqueId' => $faker->name,
                'channel_name' => 'viber',
                'assigned_to' => $faker->numberBetween(1, 5),
                'assigned_date' => now()
            ]); 
        }
        
    }
}
