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
    }
}
