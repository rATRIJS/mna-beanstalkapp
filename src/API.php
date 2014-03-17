<?php
namespace MNA\Beanstalkapp;

class API {
	const API_URL = "https://%s.beanstalkapp.com/api%s";
	
	const REQUEST_METHOD_GET = "GET";
	const REQUEST_METHOD_PUT = "PUT";
	const REQUEST_METHOD_POST = "POST";
	const REQUEST_METHOD_DELETE = "DELETE";
	
	const PARAMETER_PAGE = "page";
	const PARAMETER_PER_PAGE = "per_page";
	
	protected static $main;
	
	protected $accountName;
	protected $userName;
	protected $accessToken;
	protected $appName;
	
	protected $supportedRequestMethods = [
		self::REQUEST_METHOD_GET,
		self::REQUEST_METHOD_PUT,
		self::REQUEST_METHOD_POST,
		self::REQUEST_METHOD_DELETE
	];
	
	public static function main(API $api = null) {
		if (isset($api)) {
			static::$main = $api;
			
			return $api;
		}
		else {
			if (!isset(static::$main)) {
				throw new Exceptions\NoMainApiException("Main \\MNA\\Beanstalkapp\\API object not set.");
			}
			
			return static::$main;
		}
	}
	
	public function __construct($accountName, $userName, $accessToken, $appName = "MNA-BeanstalkappAPI") {
		if (!isset(static::$main)) {
			static::$main = $this;
		}
		
		$this->accountName = $accountName;
		$this->userName = $userName;
		$this->accessToken = $accessToken;
		$this->appName = $appName;
	}
	
	public function request($path, array $parameters = [], $method = self::REQUEST_METHOD_GET) {
		if (!in_array($method, $this->supportedRequestMethods)) {
			throw new Exceptions\InvalidRequestMethodException("Invalid request method `{$method}`.");
		}
		
		$url = "/" . trim($path, "/") . ".json";
		$url = sprintf(static::API_URL, $this->accountName, $url);
		
		if ($method === static::REQUEST_METHOD_GET && !empty($parameters)) {
			$url .= "?" . http_build_query($parameters);
		}
		
		$curlHandle = curl_init($url);
		
		curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curlHandle, CURLOPT_USERPWD, "{$this->userName}:{$this->accessToken}");
		curl_setopt($curlHandle, CURLOPT_HTTPHEADER, [
			"Content-Type" => "application/json",
			"User-Agent" => $this->appName
		]);
		curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, $method);
		if ($method !== static::REQUEST_METHOD_GET && !empty($parameters)) {
			curl_setopt($curlHandle, CURLOPT_POSTFIELDS, http_build_query($parameters));
		}
		
		$response = curl_exec($curlHandle);
		if ($response === false) {
			throw new Exceptions\RequestException("Couldn't make `{$method}` request to `{$url}`.");
		}
		
		$response = empty($response) ? [] : json_decode($response, true);
		if (!is_array($response)) {
			$response = [];
		}
		
		$responseErrors = empty($response["errors"]) ? [] : $response["errors"];
		$responseCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
		
		if (empty($responseErrors)) {
			$responseErrors = false;
		}
		else {
			$responseErrors = "`" . implode("`, `", $responseErrors) . "`";
		}
		
		if ($responseCode === 401) {
			throw new Exceptions\UnauthorisedAccessException(
				"Unauthorised access to `{$url}`. Got errors: {$responseErrors}"
			);
		}
		elseif ($responseCode !== 200) {
			throw new Exceptions\RequestException(
				"Request to `{$url}` returned a non 200 response. Got errors: {$responseErrors}"
			);
		}
		
		return $response;
	}
	
	// Accounts
	
	public function account() {
		// return Account
	}
	
	public function updateAccount(array $account) {
		//
	}
	
	// Plans
	
	public function plans() {
		// return Plans[]
	}
	
	// Users
	
	public function users(array $parameters = []) {
		$parameters = array_merge([
			'page' => null,
			'per_page' => null
		], $parameters);
		
		// return Users[]
	}
	
	public function user($userId) {
		// return User
	}
	
	public function currentUser() {
		// return User
	}
	
	public function createUser(array $user) {
		// return User
	}
	
	public function updateUser($userId, array $user) {
		// return User
	}
	
	public function deleteUser($userId) {
		// return bool
	}
	
	// Invitations
	
	public function invitations() {
		// return Invitation[]
	}
	
	public function invitation($invitationId) {
		// return Invitation
	}
	
	public function createInvitation(array $invitation) {
		// return Invitation
	}
	
	public function resendInvitation($userId) {
		// return Invitation
	}
	
	// Public Keys
	
	public function publicKeys($userId = null) {
		// return PublicKey[]
	}
	
	public function publicKey($publicKeyId) {
		// return PublicKey
	}
	
	public function createPublicKey(array $publicKey, $userId = null) {
		// return PublicKey
	}
	
	public function updatePublicKey($publicKeyId, array $publicKey) {
		// return PublicKey
	}
	
	public function deletePublicKey($publicKeyId) {
		// return bool
	}
	
	// Feed Keys
	
	public function feedKey() {
		// return FeedKey
	}
	
	public function globalFeedUrl($accountName = null, $feedKey = null) {
		if (!isset($accountName)) {
			$accountName = $this->account()->name;
		}
		
		if (!isset($feedKey)) {
			$feedKey = $this->feedKey()->name;
		}
		
		return "https://{$accountName}.beanstalkapp.com/atom/{$feedKey}";
	}
	
	public function repositoryFeedUrl($repositoryName, $accountName = null, $feedKey = null) {
		if (!isset($accountName)) {
			$accountName = $this->account()->name;
		}
		
		if (!isset($feedKey)) {
			$feedKey = $this->feedKey()->name;
		}
		
		return "https://{$accountName}.beanstalkapp.com/{$repositoryName}/activity/atom/{$feedKey}";
	}
	
	// Repositories
	
	public function repositories(array $parameters = []) {
		$parameters = array_merge([
			'page' => null,
			'per_page' => null
		], $parameters);
		
		// return Repository[]
	}
	
	public function repository($repositoryId) {
		// return Repository
	}
	
	public function branches($repositoryId) {
		// return Branch[]
	}
	
	public function tags($repositoryId) {
		// return Tag[]
	}
	
	public function createRepository(array $repository) {
		// return Repository[]
	}
	
	public function updateRepository($repositoryId, array $repository) {
		// return Repository[]
	}
	
	// Repository Imports
	
	public function repositoryImports() {
		// return RepositoryImport[]
	}
	
	public function repositoryImport($repositoryImportId) {
		// return RepositoryImport
	}
	
	public function createRepositoryImport($repositoryId, array $repositoryImport) {
		// return RepositoryImport
	}
	
	// Permissions
	
	public function permissions($userId = null) {
		// return Permission[]
	}
	
	public function createPermission($repositoryId, array $permission, $userId = null) {
		// return Permission
	}
	
	public function deletePermission($permissionID) {
		// return bool
	}
	
	// TODO: Integrations
	
	// Changesets
	
	public function changesets(array $parameters = [], $repositoryId = null) {
		$parameters = array([
			'page' => null,
			'per_page' => null,
			'order_field' => null,
			'order' => null
		], $parameters);
		
		// return Changeset[]
	}
	
	public function changeset($revision, $repositoryId) {
		// return Changeset
	}
	
	public function changesetDiffs($revision, $repositoryId) {
		// return Diff[]
	}
	
	// Comments
	
	public function comments($repositoryId, $revision = null, array $parameters = []) {
		$parameters = array_merge([
			'page' => null,
			'per_page' => null
		], $parameters);
		
		// return Comment[]
	}
	
	public function userComments($userId, array $parameters = []) {
		$parameters = array_merge([
			'page' => null,
			'per_page' => null
		], $parameters);
		
		// return Comment[]
	}
	
	public function comment($repositoryId, $commentID) {
		// return Comment
	}
	
	public function createComment($repositoryId, array $comment) {
		// return Comment
	}
	
	// TODO: Nodes
	
	// TODO: Server Environments
	
	// TODO: Release Servers
	
	// TODO: Releases
	
	// TODO: Teams
}