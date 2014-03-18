<?php
namespace MNA\Beanstalkapp;

class ChangeSetDiff extends Resource {
	protected $status;
	protected $path;
	protected $diff;
	
	public static function find($repositoryId, $revision, API $api = null) {
		if (!isset($api)) {
			$api = API::main();
		}
		
		$diffs = [];
		$parameters = [ "repository_id" => $repositoryId ];
		$response = $api->request("/changesets/{$revision}/differences", $parameters);
		if (!isset($response["files"])) {
			throw new Exceptions\MalformedResponseException("`files` key not available in API response.");
		}
		
		foreach ($response["files"] as $diff) {
			$diffs[] = new static($diff, $api);
		}
		
		return $diffs;
	}
}