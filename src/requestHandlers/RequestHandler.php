<?php
namespace jarkt\docker\requestHandlers;

/**
* Class RequestHandler
*/
abstract class RequestHandler
{

	/**
	 * @param $ch
	 * @return mixed
	 */
	abstract public function handle($ch);

	/**
	 * @return array
	 */
	public function getHeaders()
	{
		return [];
	}
}
