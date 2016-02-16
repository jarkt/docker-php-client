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
	 */
	public function __construct($target)
	{
		$this->tarFilename = tempnam(sys_get_temp_dir(), uniqid()) . '.tar';

		$phar = new \PharData($this->tarFilename);
		$phar->buildFromDirectory($target);

		$this->tarStream = fopen($this->tarFilename, 'r');
	}

	public function __destruct()
	{
		fclose($this->tarStream);
		unlink($this->tarFilename);
	}
}
