<?php
namespace jarkt\docker\requestHandlers;

/**
 * Class Files
 */
class Files extends Tar
{

	/**
	 * Files constructor.
	 * @param $target
	 * @param array $additionalHeaders
	 */
	public function __construct($target, array $additionalHeaders = [])
	{
		$this->tarFilename = sys_get_temp_dir() . '/' . uniqid() . '.tar';

		$phar = new \PharData($this->tarFilename);
		$phar->buildFromDirectory($target);

		$this->tarStream = fopen($this->tarFilename, 'r');
		$this->additionalHeaders = $additionalHeaders;
	}

	public function __destruct()
	{
		fclose($this->tarStream);
		unlink($this->tarFilename);
	}
}
