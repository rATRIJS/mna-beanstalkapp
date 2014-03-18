<?php
namespace MNA\Beanstalkapp;

class File extends Node {
	const PARAMETER_COMMIT_MESSAGE = "commit_message";
	const PARAMETER_BRANCH = "branch";
	const PARAMETER_NEW_NAME = "new_name";
	
	protected $binary;
	protected $mimeType;
	protected $language;
	protected $sizeBytes;
	protected $contents;
	
	protected $mutableProperties = [
		"path",
		"contents"
	];
	
	public static function create($repositoryId, $path, $contents, array $parameters = [], API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$parameters["path"] = $path;
		$parameters["contents"] = $contents;
		
		$response = $api->request("/repositories/{$repositoryId}/node", $parameters, API::REQUEST_METHOD_POST);
		
		return new static($response, $api);
	}
	
	public function update(array $parameters = []) {
		$parameters = array_merge($parameters, $this->exportForApi());
		
		$response = $this->api->request(
			"/repositories/{$this->repository->id}/node",
			$parameters,
			API::REQUEST_METHOD_PUT
		);
		
		$this->fill($response);
		
		return $this;
	}
}