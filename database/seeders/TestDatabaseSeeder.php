<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class TestDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        DB::table('users')->insert([
            'name' => 'admin',
            'email' => 'admin' . '@gmail.com',
            'password' => bcrypt('admin'),
        ]);
        DB::table('users')->insert([
            'name' => 'testuser',
            'email' => 'testuser' . '@gmail.com',
            'password' => bcrypt('testuser'),
        ]);
        DB::table('users')->insert([
            'name' => 'testuser2',
            'email' => 'testuser2' . '@gmail.com',
            'password' => bcrypt('testuser'),
        ]);
        DB::table('users')->insert([
            'name' => 'testuser3',
            'email' => 'testuser3' . '@gmail.com',
            'password' => bcrypt('testuser'),
        ]);

        DB::table('channels')->insert(['type'=>'private']);
        


        DB::table('channel_users')->insert([
            'channel_id' => '1',
            'user_id' => '1',
        ]);

        DB::table('channel_users')->insert([
            'channel_id' => '1',
            'user_id' => '2',
        ]);

        
        DB::table('channels')->insert(['type'=>'group', 'owner_id'=>'1', 'name'=>'testgroup']);
        
        DB::table('channel_users')->insert([
            'channel_id' => '2',
            'user_id' => '1',
        ]);
        
        DB::table('channel_users')->insert([
            'channel_id' => '2',
            'user_id' => '2',
        ]);
        
        DB::table('join_requests')->insert([
            'channel_id' => '2',
            'user_id' => '3',
            'status' => 'pending'
        ]);
    }
}
