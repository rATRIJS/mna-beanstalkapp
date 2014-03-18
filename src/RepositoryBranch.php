<?php
namespace MNA\Beanstalkapp;

class RepositoryBranch extends Resource {
	protected $branch;
	
	public static function find($repositoryId, API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$branches = [];
		$response = $api->request("/repositories/{$repositoryId}/branches");
		
		foreach ($response as $branch) {
			$branches[] = new static($branch, $api);
		}
		
		return $branches;
	}
}