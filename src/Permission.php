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
	
	protected $mutableProperties = [
		'repositoryId',
		'userId',
		'deployOnlyAccess',
		'serverEnvironmentId',
		'fullDeploymentsAccess',
		'read',
		'write'
	];
	
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
	
	public function create($repositoryId, $userId) {
		$parameters = $this->exportForApi();
		$parameters["repository_id"] = $repositoryId;
		$parameters["user_id"] = $userId;
		
		$response = $this->api->request("/permissions", $parameters, API::REQUEST_METHOD_POST);
		if (!isset($permission["permission"])) {
			throw new Exceptions\MalformedResponseException("`permission` key not available in API response.");
		}
		
		$this->fill($response["permission"]);
		
		return $this;
	}
	
	public function delete() {
		$this->api->request("/permissions/{$this->id}", [], API::REQUEST_METHOD_DELETE);
		
		return $this;
	}
}