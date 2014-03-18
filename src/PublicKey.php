<?php
namespace MNA\Beanstalkapp;

class PublicKey extends Resource {
	protected $id;
	protected $accountId;
	protected $userId;
	protected $name;
	protected $content;
	protected $updatedAt;
	protected $createdAt;
	
	protected $mutableProperties = [
		'name',
		'content'
	];
	
	public static function find($userId = null, API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$parameters = [];
		if (isset($userId)) {
			$parameters["user_id"] = $userId;
		}
		
		$keys = [];
		$response = $api->request("/public_keys", $parameters);
		
		foreach ($response as $key) {
			if (!isset($key["public_key"])) {
				throw new Exceptions\MalformedResponseException("`public_key` key not available in API response.");
			}
			
			$keys[] = new static($key["public_key"], $api);
		}
		
		return $keys;
	}
	
	public static function get($id, API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$response = $api->request("/public_keys/{$id}");
		if (!isset($response["public_key"])) {
			throw new Exceptions\MalformedResponseException("`public_key` key not available in API response.");
		}
		
		return new static($response["public_key"], $api);
	}
	
	public static function create($content, array $parameters = [], API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$parameters = array_merge($parameters, [
			"content" => $content
		]);
		
		$response = $api->request("/public_keys", $parameters, API::REQUEST_METHOD_POST);
		if (!isset($response["public_key"])) {
			throw new Exceptions\MalformedResponseException("`public_key` key not available in API response.");
		}
		
		return new static($response["public_key"], $api);
	}
	
	public function update() {
		$parameters = $this->exportForApi();
		
		$response = $this->api->request("/public_keys/{$this->id}", $parameters, API::REQUEST_METHOD_PUT);
		if (!isset($response["public_key"])) {
			throw new Exceptions\MalformedResponseException("`public_key` key not available in API response.");
		}
		
		$this->fill($response["public_key"]);
		
		return $this;
	}
	
	public function delete() {
		$this->api->request("/public_keys/{$this->id}", [], API::REQUEST_METHOD_DELETE);
		
		return $this;
	}
}