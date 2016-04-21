<?php
namespace jarkt\docker\responseHandlers;
use jarkt\docker\Response;

/**
 * Class Files
 */
class Files extends ResponseHandler
{

	private $target;
	private $removeTarget = false;


	/**
	 * Files constructor.
	 * @param Response $response
	 */
	public function __construct(Response $response)
	{
		parent::__construct($response);

		$ct = $this->getHeader('content-type');
		if($ct !== 'application/x-tar') {
			throw new \Exception(__CLASS__ . " cannot handle content type $ct - only application/x-tar");
		}
	}

	public function __destruct()
	{
		if($this->removeTarget) {
			unlink($this->target);
		}
	}

	/**
	 * @param $target
	 */
	public function saveTar($target)
	{
		if(isset($this->target)) {
			copy($this->target, $target);
		} else {
			$this->target = $target;

			$fp = fopen($this->target, 'w');
			while($this->response->streamIsActive()) {
				fwrite($fp, $this->response->readData());
			}
			fclose($fp);
		}
	}

	/**
	 * TODO: Is it possible to extract directly from the stream? With stream filters?
	 *
	 * @param $target
	 */
	public function extract($target)
	{
		if(!isset($this->target)) {
			$this->target = sys_get_temp_dir() . '/' . uniqid() . '.tar';
			$this->saveTar($this->target);

			$this->removeTarget = true;
		}

		$phar = new \PharData($this->target);
		$phar->extractTo($target);
	}
}
