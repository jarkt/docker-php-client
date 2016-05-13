<?php
namespace jarkt\docker\requestHandlers;

/**
 * Class Tar
 */
class Tar extends RequestHandler
{

	protected $tarStream;
	protected $tarFilename;
	protected $additionalHeaders;


	/**
	 * Tar constructor.
	 * @param $filename
	 * @param array $additionalHeaders
	 */
	public function __construct($filename, array $additionalHeaders = [])
	{
		$this->tarFilename = $filename;
		$this->tarStream = fopen($this->tarFilename, 'r');
		$this->additionalHeaders = $additionalHeaders;
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
		// Merge default and additional headers:
		$headers = array_merge([
			'Content-type: application/x-tar',
			'Expect:'
		], $this->additionalHeaders);

		// Clean up:
		foreach($headers as $numKey => $header) {
			$key = strtolower(substr($header, 0, strpos($header, ':')));
			$headers[$key] = $header;
			unset($headers[$numKey]);
		}
		$headers = array_values($headers);

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_INFILESIZE, filesize($this->tarFilename));
		curl_setopt($ch, CURLOPT_INFILE, $this->tarStream);
	}
}
