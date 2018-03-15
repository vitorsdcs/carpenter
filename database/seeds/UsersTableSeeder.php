<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class)->create([
            'client_id' => 'canaleducacao',
            'external_id' => 62,
            'name' => '000618900',
        ]);

        factory(App\User::class)->create([
            'client_id' => 'canaleducacao',
            'external_id' => 58926,
            'name' => '000657939',
        ]);
    }
}
