<?php
namespace MNA\Beanstalkapp;

class Plan extends Resource {
	protected $id;
	protected $name;
	protected $price;
	protected $repositories;
	protected $users;
	protected $servers;
	protected $storage;
	
	public static function find(API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$plans = [];
		$response = $api->request("/plans");
		
		foreach ($response as $plan) {
			if (!isset($plan["plan"])) {
				throw new Exceptions\MalformedResponseException("`plan` key not available in API response.");
			}
			
			$plans[] = new static($plan["plan"], $api);
		}
		
		return $plans;
	}
}