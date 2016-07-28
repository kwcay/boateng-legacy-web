<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved.
 */
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('CountrySeeder');
        $this->call('AlphabetSeeder');
    }
}
