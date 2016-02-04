<?php
namespace jarkt\docker\requestHandlers;

/**
 * Class Json
 */
class Json extends RequestHandler
{

	private $data;


	/**
	 * Json constructor.
	 * @param $data
	 */
	public function __construct($data)
	{
		$this->data = $data;
	}

	/**
	 * @param $ch
	 */
	public function handle($ch)
	{
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->data));
	}
}
