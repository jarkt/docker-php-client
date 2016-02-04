<?php
namespace jarkt\docker\requestHandlers;

/**
 * Class Files
 */
class Files extends RequestHandler
{

	private $tarStream;
	private $tarFilename;


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

	/**
	 * @param $ch
	 */
	public function handle($ch)
	{
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-type: application/x-tar',
			'Expect:'
		]);

		curl_setopt($ch, CURLOPT_INFILESIZE, filesize($this->tarFilename));
		curl_setopt($ch, CURLOPT_INFILE, $this->tarStream);
	}
}
