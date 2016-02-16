<?php
namespace jarkt\docker;

class ApiClientTest extends \PHPUnit_Framework_TestCase
{

	public function testConnect()
	{
		$docker = new ApiClient(getenv('API_PORT_2375_TCP_ADDR'), getenv('API_PORT_2375_TCP_PORT'), 'v1.21');

		$this->assertTrue($docker instanceof ApiClient);

		return $docker;
	}

	/**
	 * @depends testConnect
	 */
	public function testJsonResponse(ApiClient $docker)
	{
		$response = $docker->get('/containers/' . getenv('HOSTNAME') . '/json');
		$responseHandler = new responseHandlers\Json($response);

		$this->assertEquals(200, $response->getStatus());

		$this->assertEquals('application/json', $responseHandler->getHeader('content-type'));

		$data = $responseHandler->getData();
		$this->assertTrue(is_array($data));

		return $data;
	}

	/**
	 * @depends testConnect
	 */
	public function testLogsResponse(ApiClient $docker)
	{
		$logs = ['test1', 'test2', 'test3'];
		foreach($logs as $log) {
			error_log($log); // TODO: Be quiet!
		}

		$response = $docker->get('/containers/containers_php_1/logs', [
			'follow' => 'false',
			'stdout' => 'true',
			'stderr' => 'true',
			'since' => '0',
			'timestamps' => 'true',
			'tail' => '3'
		]);

		$this->assertEquals(200, $response->getStatus());

		$count = 0;
		$responseHandler = new responseHandlers\Logs($response);
		while(($line = $responseHandler->getLine()) !== false) {
			$this->assertStringEndsWith($logs[$count], $line);
			$count++;
		}

		$this->assertEquals(3, $count);
	}

	/**
	 * @depends testConnect
	 */
	public function testFilesResponse(ApiClient $docker)
	{
		$response = $docker->get('/containers/' . getenv('HOSTNAME') . '/archive', [
			'path' => '/project/containers/php/'
		]);

		$this->assertEquals(200, $response->getStatus());

		$responseHandler = new responseHandlers\Files($response);

		$stat = json_decode(base64_decode($responseHandler->getHeader('x-docker-container-path-stat')), true);
		$this->assertArrayHasKey('name', $stat);

		$filename = '/project/TEMP.tar';
		$responseHandler->saveTar($filename);
		$this->assertFileExists($filename);

		// Note: "php" folder should not exists. But if we request '/project/containers/php/.' (>>> "/." <<<)
		// there is an exception when extracting.
		$responseHandler->extract('/project/TEMP');
		$this->assertFileExists('/project/TEMP/php/Dockerfile');
		exec('rm -rf /project/TEMP');
		$this->assertFileNotExists('/project/TEMP/php/Dockerfile');

		unlink($filename);
		$this->assertFileNotExists($filename);
	}

	/**
	 * @depends testConnect
	 */
	public function testHeadRequest(ApiClient $docker)
	{
		$response = $docker->head('/containers/' . getenv('HOSTNAME') . '/archive', [
			'path' => '/project/containers/php'
		]);

		$this->assertEquals(200, $response->getStatus());

		$responseHandler = new responseHandlers\ResponseHandler($response);

		$stat = json_decode(base64_decode($responseHandler->getHeader('x-docker-container-path-stat')), true);
		$this->assertArrayHasKey('name', $stat);
	}

	/**
	 * @depends testConnect
	 */
	public function testFilesRequest(ApiClient $docker)
	{
		mkdir('/project/TEMP');

		$response = $docker->put('/containers/' . getenv('HOSTNAME') . '/archive',
			['path' => '/project/TEMP'],
			new requestHandlers\Files('/project/containers/php')
		);

		$this->assertEquals(200, $response->getStatus());

		$this->assertFileExists('/project/TEMP/Dockerfile');

		exec('rm -rf /project/TEMP');
	}

	/**
	 * @depends testConnect
	 */
	public function testTarRequest(ApiClient $docker)
	{
		mkdir('/project/TEMP');

		$tarFilename = tempnam(sys_get_temp_dir(), uniqid()) . '.tar';
		$phar = new \PharData($tarFilename);
		$phar->buildFromDirectory('/project/containers/php');

		$response = $docker->put('/containers/' . getenv('HOSTNAME') . '/archive',
			['path' => '/project/TEMP'],
			new requestHandlers\Tar($tarFilename)
		);

		$this->assertEquals(200, $response->getStatus());

		$this->assertFileExists('/project/TEMP/Dockerfile');

		unlink($tarFilename);
		exec('rm -rf /project/TEMP');
	}

	/**
	 * @depends testConnect
	 * @depends testJsonResponse
	 */
	public function testJsonRequest(ApiClient $docker, array $currentContainer)
	{
		$response = $docker->post('/containers/create', [], new requestHandlers\Json([
			'Image' => $currentContainer['Image'],
			'HostConfig' => [
				'VolumesFrom' => [$currentContainer['Id']]
			]
		]));

		$responseHandler = new responseHandlers\Json($response);
		$newContainer = $responseHandler->getData();
		$this->assertArrayHasKey('Id', $newContainer);

		return $newContainer['Id'];
	}

	/**
	 * @depends testConnect
	 * @depends testJsonRequest
	 */
	public function testEmptyJsonRequest(ApiClient $docker, $containerId)
	{
		$response = $docker->post("/containers/$containerId/start");
		$this->assertEquals(204, $response->getStatus());
	}

	/**
	 * @depends testConnect
	 * @depends testJsonRequest
	 */
	public function testDeleteRequest(ApiClient $docker, $containerId)
	{
		$response = $docker->delete("/containers/$containerId", ['force' => true]);

		$this->assertEquals(204, $response->getStatus());
	}
}
