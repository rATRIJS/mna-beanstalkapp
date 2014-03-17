<?php
namespace MNA\Beanstalkapp;

class Account extends Resource {
	protected $id;
	protected $ownerId;
	protected $planId;
	protected $name;
	protected $thirdLevelDomain;
	protected $timeZone;
	protected $suspended;
	protected $updatedAt;
	protected $createdAt;
	
	protected $mutableProperties = [
		'name',
		'timeZone'
	];
	
	public static function get(API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$response = $api->request("/account");
		
		if (!isset($response["account"])) {
			throw new MalformedResponseException("`account` key not available in API response.");
		}
		
		return new static($response["account"], $api);
	}
	
	public function update() {
		$this->api->request("/account", $this->exportForApi(), API::REQUEST_METHOD_PUT);
		
		return $this;
	}
}