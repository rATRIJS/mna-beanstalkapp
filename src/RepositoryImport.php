<?php
namespace MNA\Beanstalkapp;

class RepositoryImport extends Resource {
	const STATE_NEW = 'new';
	const STATE_PROGRESS = 'progress';
	const STATE_COMPLETE = 'complete';
	const STATE_FAILED = 'failed';
	
	protected $id;
	protected $accountId;
	protected $repositoryId;
	protected $state;
	protected $uri;
	protected $updatedAt;
	protected $createdAt;
	
	protected $mutableProperties = [
		'uri'
	];
	
	public static function find(API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$imports = [];
		$response = $api->request("/repository_imports");
		
		foreach ($response as $import) {
			if (!isset($import["repository_import"])) {
				throw new Exceptions\MalformedResponseException(
					"`repository_import` key not available in API response."
				);
			}
			
			$imports[] = new static($import["repository_import"], $api);
		}
		
		return $imports;
	}
	
	public static function get($id, API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$response = $api->request("/repository_imports/{$id}");
		if (!isset($response["repository_import"])) {
			throw new Exceptions\MalformedResponseException("`repository_import` key not available in API response.");
		}
		
		return new static($response["repository_import"], $api);
	}
	
	public static function create($repositoryId, $uri, API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$parameters = [
			"uri" => $uri
		];
		
		$response = $api->request("/{$repositoryId}/repository_imports", $parameters, API::REQUEST_METHOD_POST);
		if (!isset($response["repository_import"])) {
			throw new Exceptions\MalformedResponseException("`repository_import` key not available in API response.");
		}
		
		return new static($response["repository_import"], $api);
	}
	
	public function isInState($state) {
		return ($this->state === $state);
	}
	
	public function isNew() {
		return $this->isInState(static::STATE_NEW);
	}
	
	public function isInProgress() {
		return $this->isInState(static::STATE_PROGRESS);
	}
	
	public function isComplete() {
		return $this->isInState(static::STATE_COMPLETE);
	}
	
	public function hasFailed() {
		return $this->isInState(static::STATE_FAILED);
	}
}