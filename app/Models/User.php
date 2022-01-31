<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_name',
        'email',
        'joining_date',
        'bio',
        'avatar_url',
        'following',
        'followers',
        'public_repos',
        'popularity',
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
    ];
	
	public $timestamps = false;
	
	public function repositories(){
		return $this->hasMany('App\Models\Repository','user_id','id');
	}
	
	public function getResponseData($repository_flag = false){
		$userData = [
			'id' => $this->id,
			'user_name' => $this->user_name,
			'email' => $this->email,
			'joining_date' => $this->joining_date,
			'bio' => $this->bio,
			'avatar_url' => $this->avatar_url,
			'following' => $this->following,
			'followers' => $this->followers,
			'public_repos' => $this->public_repos
		];
		
		if($repository_flag){
			$repositoriesData=collect();
			$this->repositories->each(function ($repo) use ($repositoriesData){
				$repositoriesData->push($repo);
			});
			$userData['repositories'] = $repositoriesData->toArray();
		}
			
			return $userData;
	}
}
