<?php
namespace jarkt\docker\responseHandlers;

/**
 * Class Logs
 */
class Logs extends ResponseHandler
{

	private $line = '';


	public function getLine()
	{
		do {
			$this->line .= $this->response->readData();
			$lineEnd = strpos($this->line, "\n");
		} while($lineEnd === false && $this->response->streamIsActive());

		if($lineEnd === false) {
			// Stream closed, get remaining data:
			$lineEnd = strlen($this->line);
		}

		$nextLine = substr($this->line, $lineEnd + 1);
		$line = substr($this->line, 0, $lineEnd);
		$this->line = $nextLine;

		return $line;
	}
}
