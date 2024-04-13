<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',  
        'password' => 'hashed',
    ];

    public function login(string $username, string $password)
    {
        //Auth::attempt creates the session needed for auth if successful 
        if(Auth::attempt(['name' => $username, 'password' =>$password])){
            return 200;
        }else{
            abort(response()->json(['errorMsg' => 'Invalid Username or Password.'], 403));
        }
    }
    
    public function signUp(string $username, string $password, string $email){

        if (User::where('email', $email)->exists()) {
            abort(response()->json('Email already in-use.', 403));
        }

        if (User::where('name', $username)->exists()) {
            abort(response()->json('Username already in-use.', 403));
        }
        
        $User = User::create([
            'name' => $username,
            'password' => $password,
            'email' => $email
        ]);
        return $User;
    }
    public function Channels(): BelongsToMany
    {
        return $this->belongsToMany(Channel::class, 'channel_users')->withPivot('unread_count');
    }
    
    public function messages(): HasMany{
        return $this->hasMany(Message::class);
    }

    public function requests(): HasMany{
        return $this->hasMany(JoinRequest::class);
    }
}
