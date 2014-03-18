<?php
namespace MNA\Beanstalkapp;

class RepositoryTag extends Resource {
	protected $tag;
	
	public static function find($repositoryId, API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$tags = [];
		$response = $api->request("/repositories/{$repositoryId}/tags");
		
		foreach ($response as $tag) {
			$tags[] = new static($tag, $api);
		}
		
		return $tags;
	}
}