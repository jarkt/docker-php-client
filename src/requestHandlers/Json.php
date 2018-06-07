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
	 * @param array|null $data
	 */
	public function __construct(array $data = null)
	{
		$this->data = $data;
	}

	/**
	 * @param $ch
	 */
	public function handle($ch)
	{
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->data));
	}

	/**
	 * @return array
	 */
	public function getHeaders()
	{
		return [
			'Content-type: application/json',
			'Expect:'
		];
	}
}
