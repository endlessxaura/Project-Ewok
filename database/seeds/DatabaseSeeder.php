<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * NOTE: YOU MUST HAVE AN EMPTY DATABASE WITH NEW INCREMENTS
     *
     * @return void
     */
    public function run()
    {
        factory(App\Geolocation::class, 500)->create([
            'locationType' => 'farm'
            ]);

        for($i = 1; $i <= 500; $i++){ 
            factory(App\Farm::class)->create([
                'geolocationID' => $i
                ]);
        }
    }
}
