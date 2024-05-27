<?php

namespace Tests\Feature;

use App\Models\JoinRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JoinRequestTest extends TestCase
{

    use RefreshDatabase;
    protected $seed = true;

    public function test_get_requests(): void
    {
        $user = User::query()->first();
        $response = $this->actingAs($user)->get('_api/chats/2/requests');

        $response->assertStatus(200);
        $response->assertJsonStructure(['*'=>['id', 'user_id', 'name']]);
    }

    public function test_post_join_request(): void
    {
        $user = User::query()->find(2);
        $response = $this->actingAs($user)->post('_api/requests', ['channelName'=>'testgroup']);
        $testJoinRequest = JoinRequest::query()->find($response->json('id'));

        $response->assertStatus(200);
        $this->assertNotEmpty($testJoinRequest->id);
    }
    
    public function test_post_resolve_request(): void
    {
        $user = User::query()->first();
        $response = $this->actingAs($user)->post('_api/requests/1', ['accepted'=>true]);
        $testJoinRequest = JoinRequest::query()->find($response->json('id'));
        
        $response->assertStatus(200);
        $this->assertEquals('accepted', $testJoinRequest->status);
    }
    
}
