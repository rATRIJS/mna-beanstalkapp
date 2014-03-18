<?php
namespace MNA\Beanstalkapp;

class ChangeSet extends Resource {
	const PARAMETER_ORDER = "order";
	const PARAMETER_ORDER_FIELD = "order_field";
	
	protected $id;
	protected $accountId;
	protected $repositoryId;
	protected $userId;
	protected $revision;
	protected $hashId;
	protected $author;
	protected $email;
	protected $message;
	protected $changedFiles;
	protected $changedDirs;
	protected $changedProperties;
	protected $time;
	protected $tooLarge;
	
	public static function find(array $parameters = [], API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$changesets = [];
		$response = $api->request("/changesets", $parameters);
		
		foreach ($response as $changeset) {
			if (!isset($changeset["revision_cache"])) {
				throw new Exceptions\MalformedResponseException("`revision_cache` key not available in API response.");
			}
			
			$changesets[] = new static($changeset["revision_cache"], $api);
		}
		
		return $changesets;
	}
	
	public static function findForRepository($repositoryId, array $parameters = [], API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$parameters["repository_id"] = $repositoryId;
		
		$changesets = [];
		$response = $api->request("/changesets/repository", $parameters);
		
		foreach ($response as $changeset) {
			if (!isset($changeset["revision_cache"])) {
				throw new Exceptions\MalformedResponseException("`revision_cache` key not available in API response.");
			}
			
			$changesets[] = new static($changeset["revision_cache"], $api);
		}
		
		return $changesets;
	}
	
	public static function get($repositoryId, $revision, API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$parameters = [ "repository_id" => $repositoryId ];
		$response = $api->request("/changesets/{$revision}", $parameters);
		if (!isset($response["revision_cache"])) {
			throw new Exceptions\MalformedResponseException("`revision_cache` key not available in API response.");
		}
		
		return new static($response["revision_cache"], $api);
	}
	
	public function diff() {
		return ChangeSetDiff::find($this->repositoryId, $this->revision, $this->api);
	}
}