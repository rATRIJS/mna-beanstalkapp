<?php
namespace MNA\Beanstalkapp;

class Permission extends Resource {
	protected $id;
	protected $repositoryId;
	protected $userId;
	protected $serverEnvironmentId;
	protected $fullDeploymentsAccess;
	protected $deployOnlyAccess;
	protected $read;
	protected $write;
	
	public static function find($userId, API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$permissions = [];
		$response = $api->request("/permissions/{$userId}");
		
		foreach ($response as $permission) {
			if (!isset($permission["permission"])) {
				throw new Exceptions\MalformedResponseException("`permission` key not available in API response.");
			}
			
			$permissions[] = new static($permission["permission"], $api);
		}
		
		return $permissions;
	}
	
	public static function create($repositoryId, $userId, array $parameters = [], API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$parameters = array_merge($parameters, [
			"repository_id" => $repositoryId,
			"user_id" => $userId
		]);
		
		$response = $api->request("/permissions", $parameters, API::REQUEST_METHOD_POST);
		if (!isset($response["permission"])) {
			throw new Exceptions\MalformedResponseException("`permission` key not available in API response.");
		}
		
		return new static($response["permission"], $api);
	}
	
	public function delete() {
		$this->api->request("/permissions/{$this->id}", [], API::REQUEST_METHOD_DELETE);
		
		return $this;
	}
}