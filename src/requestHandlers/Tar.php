<?php
namespace jarkt\docker\requestHandlers;

/**
 * Class Tar
 */
class Tar extends RequestHandler
{

	protected $tarStream;
	protected $tarFilename;


	/**
	 * Tar constructor.
	 * @param $filename
	 */
	public function __construct($filename)
	{
		$this->tarFilename = $filename;
		$this->tarStream = fopen($this->tarFilename, 'r');
	}

	public function __destruct()
	{
		fclose($this->tarStream);
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
