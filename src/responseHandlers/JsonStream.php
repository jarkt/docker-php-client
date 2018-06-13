<?php
namespace jarkt\docker\responseHandlers;

/**
 * Class JsonStream
 * @package jarkt\docker\responseHandlers
 */
class JsonStream extends Json
{

	/**
	 * @var string
	 */
	private $puffer;


	/**
	 * @return mixed|null
	 */
	public function getNextData()
	{
		while (($breakPoint = strpos($this->puffer, "\n")) === false && $this->response->streamIsActive()) {
			$this->puffer = $this->response->readData();
		}

		if ($breakPoint) {
			$json = substr($this->puffer, 0, $breakPoint);
			$this->puffer = substr($this->puffer, $breakPoint + 1);
			$rtn = json_decode($json, true);

			if (($error = json_last_error()) !== JSON_ERROR_NONE) {
				throw new \jarkt\docker\exceptions\InvalidJson(sprintf("Error %s on json_decode. JSON: %s", $error, $json));
			}

			return $rtn;
		}

		return null;
	}
}
