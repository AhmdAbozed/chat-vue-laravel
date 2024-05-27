<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChannelTest extends TestCase
{

    use RefreshDatabase;
    protected $seed = true;
    public function test_get_channels(): void
    {
        $user = User::query()->first();
        $response = $this->actingAs($user)->get('_api/chats');

        $response->assertStatus(200);
        $response->assertJsonStructure(['*'=>['id', 'name', 'unreadCount', 'type']]);
    }

    public function test_get_channel_members(): void
    {
        $user = User::query()->first();
        $response = $this->actingAs($user)->get('_api/chats/2/users');

        $response->assertStatus(200);
        $response->assertJsonStructure(['*'=>['id', 'name']]);
    }

    public function test_post_private_channel(): void
    {
        $user = User::query()->first();
        $response = $this->actingAs($user)->post('_api/chats', ['receiverName'=>'testuser2']);
        $testChannel = Channel::query()->find($response->json('id'));

        $response->assertStatus(200);
        $this->assertNotEmpty($testChannel->id);
        $this->assertEmpty($testChannel->name); //private channels have no names
    }
    
    public function test_post_group_channel(): void
    {
        $user = User::query()->first();
        $response = $this->actingAs($user)->post('_api/chats/groups', ['channelName'=>'testchannel']);
        $response->assertStatus(200);

        $testChannel = Channel::query()->where('name', '=' ,'testchannel')->first();
        $this->assertEquals('testchannel', $testChannel->name);
    }
}
