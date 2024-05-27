<?php

namespace Tests\Feature;

use App\Events\NewMsgSent;
use App\Models\Message;
use App\Models\User;
use Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;

class MessageTest extends TestCase
{

    use RefreshDatabase;
    protected $seed = true;
    
    public function test_get_messages(): void
    {
        $user = User::query()->first();
        $response = $this->actingAs($user)->get('_api/chats/1/messages?withFileToken=0');

        $response->assertStatus(200);
        $response->assertJsonStructure(['messages']);
    }

    public function test_post_message(): void
    {
        Event::fake();
        
        $user = User::query()->first();  
        $response = $this->actingAs($user)->post('_api/chats/1/messages', ['message'=>'test message']);
        $testMessage = Message::query()->where('content', '=' ,'test message')->first();
        
        Event::assertDispatched(NewMsgSent::class);
        $response->assertStatus(200);
        $this->assertEquals('test message', $testMessage->content);
    }
}
