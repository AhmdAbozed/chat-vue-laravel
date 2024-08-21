<?php

namespace App\Services;

use App\Models\Channel;
use App\Models\Message;
use App\Models\User;
use Auth;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class UserService
{
    private $user;
    private $channel;
    private $fileHandler;
    public function __construct(User $user, Channel $channel, BackBlazeService $fileHandler)
    {
        $this->user = $user;
        $this->channel = $channel;
        $this->fileHandler = $fileHandler;
    }
    public function createDemoAccount(string $username, string $password, string $email){
        $user = $this->user->signUp($username, $password, $email);
        
        $channel1 = $this->channel->query()->create(['type' => 'private']);
        $channel1->users()->attach($user->id);
        $channel1->users()->attach(1);
        
        $channel2 = $this->channel->query()->create(['type' => 'private']);
        $channel2->users()->attach($user->id);
        $channel2->users()->attach(2);

        Auth::attempt(['name' => $username, 'password' =>$password]); 
        return $user;
    }
}
