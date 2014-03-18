<?php
namespace MNA\Beanstalkapp;

abstract class Node extends Resource {
	const PARAMETER_PATH = "path";
	const PARAMETER_REVISION = "revision";
	const PARAMETER_SIZE = "size";
	const PARAMETER_CONTENTS = "contents";
	
	protected $repository;
	protected $name;
	protected $path;
	protected $revision;
	
	public static function dispatch(array $node, API $api = null) {
		if ($node["file"]) {
			return new File($node, $api);
		}
		elseif ($node["directory"]) {
			return new Directory($node, $api);
		}
		else {
			throw new Exceptions\MalformedResponseException("Unknown node.");
		}
	}
	
	public static function get($repositoryId, array $parameters = [], API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$response = $api->request("/repositories/{$repositoryId}/node", $parameters);
		
		return static::dispatch($response, $api);
	}
	
	public function fill(array $properties) {
		if (!empty($properties["repository"])) {
			if (!is_array($properties["repository"])) {
				$properties["repository"] = [];
			}
			
			$properties["repository"] = new Repository($properties["repository"], $this->api);
		}
		
		return parent::fill($properties);
	}
	
	public function rename($newName, array $parameters = []) {
		$parameters = array_merge($parameters, $this->exportForApi());
		$parameters["new_filename"] = $newName;
		unset($parameters["contents"]);
		
		$response = $this->api->request(
			"/repositories/{$this->repository->id}/node/rename",
			$parameters,
			API::REQUEST_METHOD_POST
		);
		
		$this->fill($response);
		
		return $this;
	}
	
	public function delete(array $parameters = []) {
		$parameters = array_merge($parameters, $this->exportForApi());
		
		$this->api->request("/repositories/{$this->repository->id}/node", $parameters, API::REQUEST_METHOD_DELETE);
		
		return $this;
	}
}