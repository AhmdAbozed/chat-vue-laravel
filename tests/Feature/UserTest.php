<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{

    use RefreshDatabase;
    
    public function test_signup(): void
    {
        $response = $this->post('_api/users/signup', ['username'=>'testAccount', 'password'=>'testPassword', 'email'=>'testEmail@testmail.com']);
        $response->assertStatus(200);
        $this->assertEquals(User::query()->where('email', '=', 'testEmail@testmail.com')->first(['name', 'email'])->name, 'testAccount');
    }
    
    public function test_signin(): void
    {
        User::query()->create(['name'=>'testAccount', 'password'=>'testPassword', 'email'=>'testEmail@testmail.com']);
        $response = $this->post('_api/users/login', ['username'=>'testAccount', 'password'=>'testPassword']);
        $response->assertStatus(200);
    }
}
