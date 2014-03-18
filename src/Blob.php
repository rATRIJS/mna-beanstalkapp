<?php
namespace MNA\Beanstalkapp;

class Blob extends Resource {
	protected $content;
	protected $contentType;
	
	public static function get($id, $repositoryId, array $parameters = [], API $api = null) {
		$parameters["id"] = $id;
		
		if (!isset($api)) {
			$api = API::main();
		}
		
		$response = $api->request("/repositories/{$repositoryId}/blob", $parameters);
		
		return new static($response, $api);
	}
}