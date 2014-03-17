<?php
namespace MNA\Beanstalkapp;

class FeedKey extends Resource {
	protected $id;
	protected $accountId;
	protected $userId;
	protected $key;
	protected $updatedAt;
	protected $createdAt;
	
	public static function get(API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$response = $api->request("/feed_key");
		if (!isset($response["feed_key"])) {
			throw new Exceptions\MalformedResponseException("`feed_key` key not available in API response.");
		}
		
		return new static($response["feed_key"], $api);
	}
}