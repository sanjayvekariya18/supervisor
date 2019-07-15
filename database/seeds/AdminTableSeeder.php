<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->insert([
            'id' => '1',
            'first_name' => 'Keval',
            'last_name' => 'Savani',
            'contact_number' => '7405449509',
            'email' => 'keval.savani@gmail.com',
            'username' => 'admin',
            'password' => bcrypt('admin@123'),
            'created_at' => '2019-02-28 02:43:38',
            'updated_at' => '2019-02-28 02:43:38',
        ]);
    }
}
