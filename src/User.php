<?php
namespace MNA\Beanstalkapp;

class User extends Resource {
	protected $id;
	protected $accountId;
	protected $login;
	protected $email;
	protected $name;
	protected $firstName;
	protected $lastName;
	protected $owner;
	protected $admin;
	protected $timezone;
	protected $updatedAt;
	protected $createdAt;
	
	protected $mutableProperties = [
		'email',
		'name',
		'admin',
		'timezone'
	];
	
	public static function find(array $parameters = [], API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$parameters = array_merge([
			$api::PARAMETER_PER_PAGE => 30
		], $parameters);
		
		$users = [];
		$response = $api->request("/users", $parameters);
		
		foreach ($response as $user) {
			if (!isset($user["user"])) {
				throw new Exceptions\MalformedResponseException("`user` key not available in API response.");
			}
			
			$users[] = new static($user["user"], $api);
		}
		
		return $users;
	}
	
	public static function get($id, API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$response = $api->request("/users/{$id}");
		if (!isset($response["user"])) {
			throw new Exceptions\MalformedResponseException("`user` key not available in API response.");
		}
		
		return new static($response["user"], $api);
	}
	
	public static function current(API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$response = $api->request("/users/current");
		if (!isset($response["user"])) {
			throw new Exceptions\MalformedResponseException("`user` key not available in API response.");
		}
		
		return new static($response["user"], $api);
	}
	
	public static function create($login, $email, $name, $password, array $parameters = [], API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$parameters = array_merge($parameters, [
			"login" => $login,
			"email" => $email,
			"name" => $name,
			"password" => $password
		]);
		
		$response = $api->request("/users", $parameters, API::REQUEST_METHOD_POST);
		if (!isset($response["user"])) {
			throw new Exceptions\MalformedResponseException("`user` key not available in API response.");
		}
		
		return new static($response["user"], $api);
	}
	
	public function update(array $parameters = []) {
		$parameters = array_merge($this->exportForApi(), $parameters);
		if (empty($parameters["timezone"])) {
			unset($parameters["timezone"]);
		}
		
		$response = $this->api->request("/users/{$this->id}", $parameters, API::REQUEST_METHOD_PUT);
		if (!isset($response["user"])) {
			throw new Exceptions\MalformedResponseException("`user` key not available in API response.");
		}
		
		$this->fill($response["user"]);
		
		return $this;
	}
	
	public function delete() {
		$this->api->request("/users/{$this->id}", [], API::REQUEST_METHOD_DELETE);
		
		return $this;
	}
}