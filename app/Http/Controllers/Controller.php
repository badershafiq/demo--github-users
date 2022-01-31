<?php

namespace App\Http\Controllers;

use App\Models\Repository;
use App\Models\User;
use App\Models\UserPopularity;
use Carbon\Carbon;
use GrahamCampbell\GitHub\Facades\GitHub as GitHubService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController {
	
	/**
	 * @param \Illuminate\Http\Request $request
	 * @return mixed
	 */
	public function index (Request $request) {
		$query = $request->query('page');
		if ($query == NULL) {
			$response = GitHubService::search()->users(
				'repos:>0',
				'repositories'
			);
			Log::channel('search')->info('User Index Query ==>'."/search/repositories/?q=repos:>0&s=repositories");
			
			$users = Collect($response['items']);
			$users->each(function ($user) {
				$currentUser = User::where('user_name', $user['login'])->first(
				);
				if (!isset($currentUser)) {
					$userDetails = GitHubService::users()->show($user['login']);
					Log::channel('search')->info('User detail Query ==>'.'/users/{username}');
					User::create(self::userData($userDetails));
				}
			});
		}
		
		return User::orderBy(
			'popularity',
			'DESC'
		)->orderBy(
			'public_repos',
			'DESC'
		)->simplePaginate(3)
		;
	}
	
	/**
	 * @param $details
	 * @return array
	 */
	protected function userData ($details)
	:array {
		return ['user_name' => $details['login'],
		        'email' => $details['email'],
		        'joining_date' => $details['created_at'],
		        'bio' => $details['bio'],
		        'gravatar_url' => $details['avatar_url'],
		        'following' => $details['following'],
		        'followers' => $details['followers'],
		        'public_repos' => $details['public_repos'],
		        'popularity' => 1];
	}
	
}
