<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved.
 */
use Illuminate\Database\Seeder;
use App\Models\Alphabet;

class AlphabetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Seeding Latin alphabets...');

        Alphabet::firstOrCreate([
            'name' => 'Twi Alphabet',
            'transliteration' => 'Twi Alphabet',
            'code' => 'twi-Latn',
            'script_code' => 'Latn',
            'letters' => 'abdeɛfghiklmnoɔprstuwy'.
                "\n".
                'ABDEƐFGHIKLMNOƆPRSTUWY',
        ]);
    }
}
