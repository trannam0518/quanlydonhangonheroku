<?php

use Illuminate\Database\Seeder;

class Orders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Orders::class,20)->create();
    }
}
