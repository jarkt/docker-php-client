<?php
namespace jarkt\docker\responseHandlers;
use jarkt\docker\Response;

/**
 * Class Json
 */
class Json extends ResponseHandler
{

	/**
	 * Json constructor.
	 * @param Response $response
	 */
	public function __construct(Response $response)
	{
		parent::__construct($response);

		$ct = $this->getHeader('content-type');
		if($ct !== 'application/json') {
			throw new \Exception(__CLASS__ . " cannot handle content type $ct - only application/json");
		}
	}

	/**
	 * @return mixed
	 */
	public function getData()
	{
		return json_decode($this->getBody(), true);
	}
}
