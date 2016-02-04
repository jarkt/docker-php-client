<?php
namespace jarkt\docker\responseHandlers;
use jarkt\docker\Response;

/**
 * Class ResponseHandler
 */
class ResponseHandler
{

	protected $response;
	private $header;
	private $body;


	/**
	 * Json constructor.
	 * @param Response $response
	 */
	public function __construct(Response $response)
	{
		$this->response = $response;

		// Read header and parse:
		$headerLines = explode("\r\n", $this->response->readData($this->response->getHeaderSize()));
		$headerLines = array_slice($headerLines, 1, count($headerLines) - 3);
		$this->header = [];
		foreach($headerLines as $headerLine) {
			preg_match('#(.*?): (.*)#', $headerLine, $matches);
			$this->header[strtolower($matches[1])] = $matches[2];
		}
	}

	/**
	 * @param null $key
	 * @return array
	 */
	public function getHeader($key = null)
	{
		return $key ? $this->header[$key] : $this->header;
	}

	/**
	 * @return mixed
	 */
	public function getBody()
	{
		if(!isset($this->body)) {
			$this->response->waitForBody();
			$this->body = $this->response->readData();
		}
		return $this->body;
	}
}
