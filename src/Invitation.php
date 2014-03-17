<?php
namespace MNA\Beanstalkapp;

class Invitation extends Resource {
	protected $id;
	protected $accountId;
	protected $creatorId;
	protected $creatorName;
	protected $creatorEmail;
	protected $secureToken;
	protected $signupUrl;
	protected $updatedAt;
	protected $createdAt;
	
	protected $mutableProperties = [
		'email',
		'name'
	];
	
	public static function find(API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$invitations = [];
		$response = $api->request("/invitations");
		
		foreach ($response as $invitation) {
			if (!isset($invitation["invitation"])) {
				throw new Exceptions\MalformedResponseException("`invitation` key not available in API response.");
			}
			
			$invitations[] = new static($invitation["invitation"], $api);
		}
		
		return $invitations;
	}
	
	public static function get($id, API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$response = $api->request("/invitations/{$id}");
		if (!isset($response["invitation"])) {
			throw new Exceptions\MalformedResponseException("`invitation` key not available in API response.");
		}
		
		return new static($response["invitation"], $api);
	}
	
	public static function resend($userId, API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$response = $api->request("/invitations/{$userId}/resend", [], API::REQUEST_METHOD_PUT);
		if (!isset($response["invitation"])) {
			throw new Exceptions\MalformedResponseException("`invitation` key not available in API response.");
		}
		
		return new static($response["invitation"], $api);
	}
	
	public function create() {
		$parameters = $this->exportForApi();
		
		$response = $api->request("/invitations", $parameters, API::REQUEST_METHOD_POST);
		if (!isset($response["invitation"])) {
			throw new Exceptions\MalformedResponseException("`invitation` key not available in API response.");
		}
		
		$this->fill($response["invitation"]);
		
		return $this;
	}
}