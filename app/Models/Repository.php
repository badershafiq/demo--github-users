<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
class Repository extends Model
{
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'user_id',
		'forks',
		'name',
		'stars',
	];
	
	
	
	public function getResponseData(){
		
		return [
			'name'=>$this->name,
			'forks'=>$this->forks,
			'stars'=>$this->stars,
		];
	}
}
