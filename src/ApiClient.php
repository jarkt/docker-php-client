<?php
namespace jarkt\docker;
use jarkt\docker\requestHandlers\RequestHandler;

/**
 * Class ApiClient
 */
class ApiClient
{

	private $host;
	private $port;
	private $version;


	/**
	 * ApiClient constructor.
	 * @param $host
	 * @param $port
	 * @param null $version
	 */
	public function __construct($host, $port, $version = null)
	{
		$this->host = $host;
		$this->port = $port;
		$this->version = $version;
	}

	/**
	 * @param $path
	 * @param array $params
	 * @return string
	 * @throws Exception
	 */
	private function getUrl($path, array $params = [])
	{
		if($path[0] !== '/') {
			throw new \Exception('$path must begin with /');
		}
		$version = $this->version ? '/'.$this->version : '';
		$url = "http://{$this->host}:{$this->port}$version$path";
		$query = http_build_query($params);
		$url = strlen($query) ? "$url?$query" : $url;
		return $url;
	}

	/**
	 * @param $url
	 * @param callable|null $callback
	 * @param RequestHandler|null $requestHandler
	 * @return Response
	 */
	private function makeRequest($url, callable $callback = null, RequestHandler $requestHandler = null)
	{
		$response = new Response();
		$ch = $response->getCurlHandle();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $str) use($response) {
			$response->writeData($str);
			return strlen($str);
		});

		if(is_callable($callback)) {
			call_user_func($callback, $ch);
		}

		if($requestHandler instanceof RequestHandler) {
			$requestHandler->handle($ch);
		}

		$response->waitForHeader();

		return $response;
	}

	/**
	 * @param $path
	 * @param array $params
	 * @return Response
	 * @throws Exception
	 */
	public function get($path, array $params = [], RequestHandler $requestHandler = null)
	{
		$url = $this->getUrl($path, $params);
		return $this->makeRequest($url, null, $requestHandler);
	}

	/**
	 * @param $path
	 * @param array $params
	 * @return Response
	 * @throws Exception
	 */
	public function head($path, array $params = [], RequestHandler $requestHandler = null)
	{
		$url = $this->getUrl($path, $params);
		$callback = function($ch) {
			curl_setopt($ch, CURLOPT_NOBODY, true);
		};
		return $this->makeRequest($url, $callback, $requestHandler);
	}

	/**
	 * @param $path
	 * @param array $params
	 * @param RequestHandler|null $requestHandler
	 * @return Response
	 * @throws \Exception
	 */
	public function delete($path, array $params = [], RequestHandler $requestHandler = null)
	{
		$url = $this->getUrl($path, $params);
		$callback = function($ch) {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		};
		return $this->makeRequest($url, $callback, $requestHandler);
	}

	/**
	 * @param $path
	 * @param array $params
	 * @param RequestHandler|null $requestHandler
	 * @return Response
	 * @throws Exception
	 */
	public function post($path, array $params = [], RequestHandler $requestHandler = null)
	{
		$url = $this->getUrl($path, $params);
		$callback = function($ch) {
			curl_setopt($ch, CURLOPT_POST, 1);
		};
		return $this->makeRequest($url, $callback, $requestHandler);
	}

	/**
	 * @param $path
	 * @param array $params
	 * @param RequestHandler|null $requestHandler
	 * @return Response
	 * @throws Exception
	 */
	public function put($path, array $params = [], RequestHandler $requestHandler = null)
	{
		$url = $this->getUrl($path, $params);
		$callback = function($ch) {
			curl_setopt($ch, CURLOPT_PUT, 1);
		};
		return $this->makeRequest($url, $callback, $requestHandler);
	}
}
