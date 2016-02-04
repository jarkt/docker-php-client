<?php
namespace jarkt\docker;

/**
 * Class Response
 */
class Response
{

	private $curlMultiHandle;
	private $curlHandle;
	private $data;

	/**
	 * Response constructor.
	 */
	public function __construct()
	{
		$this->curlMultiHandle = curl_multi_init();
		$this->curlHandle = curl_init();
		curl_multi_add_handle($this->curlMultiHandle, $this->curlHandle);

		$this->data = '';
	}

	public function __destruct()
	{
		curl_multi_remove_handle($this->curlMultiHandle, $this->curlHandle);
		curl_multi_close($this->curlMultiHandle);
	}

	/**
	 * @return mixed
	 */
	public function getCurlHandle()
	{
		return $this->curlHandle;
	}

	/**
	 * @param int $length
	 * @return string
	 */
	public function readData($length = 0)
	{
		$data = $this->data;
		if($length) {
			$data = substr($data, 0, $length);
			$this->data = substr($this->data, $length);
		} else {
			$this->data = '';
		}
		return $data;
	}

	/**
	 * @param string $data
	 */
	public function writeData($data)
	{
		$this->data .= $data;
	}

	public function waitForHeader()
	{
		$active = null;
		do {
			curl_multi_exec($this->curlMultiHandle, $active);
		} while($active && $this->getHeaderSize() === 0);
	}

	public function waitForBody()
	{
		$active = null;
		do {
			curl_multi_exec($this->curlMultiHandle, $active);
		} while($active);
	}

	public function streamIsActive()
	{
		curl_multi_select($this->curlMultiHandle);
		$active = null;
		do {
			$mrc = curl_multi_exec($this->curlMultiHandle, $active);
		} while($mrc == CURLM_CALL_MULTI_PERFORM);
		if($active) {
			return true;
		}
		$info = curl_multi_info_read($this->curlMultiHandle);
		return is_array($info);
	}

	/**
	 * @return mixed
	 */
	public function getStatus()
	{
		return curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE);
	}

	/**
	 * @return mixed
	 */
	public function getHeaderSize()
	{
		return curl_getinfo($this->curlHandle, CURLINFO_HEADER_SIZE);
	}
}
