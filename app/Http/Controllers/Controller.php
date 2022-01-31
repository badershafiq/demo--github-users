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
	
	/**
	 * @param \Illuminate\Http\Request $request
	 * @return mixed
	 */
	public function searchQuery (Request $request) {
		$query = $request->query('q');
		if ($query == NULL) {
			$query = 'repos:>0';
		}
		$response = GitHubService::search()->users($query, 'repositories');
		Log::channel('search')->info('User search Query ==>'."/search/repositories/?q=query&s=repositories");
		
		$users = Collect($response['items']);
		$ids = Collect();
		$users->each(function ($user) use ($ids) {
			$currentUser = User::where('user_name', $user['login'])->first();
			if (!isset($currentUser)) {
				$userDetails = GitHubService::users()->show($user['login']);
				Log::channel('search')->info('User detail Query ==>'.'GitHubService::users()->show($user["login"])');
				$user = User::create(self::userData($userDetails));
			}
			else {
				$user = $currentUser;
			}
			$ids->push($user->id);
		});
		
		return User::whereIn('id', $ids->toArray())->orderBy(
			'popularity',
			'DESC'
		)->orderBy(
			'public_repos',
			'DESC'
		)->simplePaginate(3)->withQueryString()->toArray();
	}
	
	/**
	 * @param $id
	 * @return mixed
	 */
	public function findById ($id) {
		$user = User::findOrFail($id);
		$user->popularity = (int)$user->popularity + 1;
		$user->save();
		UserPopularity::create(
			[
				'popularity'=> $user->popularity,
				'user_id'=>$user->id
			]
		);
		$repositories = Collect(
			GitHubService::users()->repositories($user->user_name)
		);
		Log::channel('search')->info('Repositories against user Query ==>'.'/users/{username}/repositories');
		
		$repositories->each(function ($repo) use ($id) {
			$currentRepo = Repository::where('user_id', $id)->where(
					'name',
					$repo['name']
				)->first();
			if (!isset($currentRepo)) {
				Repository::create(self::repositoryData($repo, $id));
			}
		});
		
		return $user->getResponseData(true);
	}
	
}
