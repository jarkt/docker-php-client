# Usage

## Installation
Install via composer:
```
{
	"require": {
		"jarkt/docker-remote-php": "0.9.*"
	}
}
```

## Usage
The ApiClient connects to the [Docker Remote API](https://docs.docker.com/engine/reference/api/docker_remote_api/) via
HTTP. You can use the container ["jarkt/docker-remote-api"](https://hub.docker.com/r/jarkt/docker-remote-api/) to make
the API available.

First create an instance of the ApiClient.
```
\jarkt\docker\ApiClient::construct ($host, $port [, $version ] )
```

Example:
```
$docker = new ApiClient(getenv('API_PORT_2375_TCP_ADDR'), getenv('API_PORT_2375_TCP_PORT'), 'v1.21');
```
(Haha, like 1.21 Gigawatt :-)

You can use various types of request methods to perform your API call. There are get, head, delete, post and put.
The signatures are the same:

* `method ($path [, array $params [, requestHandlers\RequestHandler $requestHandler ]] )`

For your call you have to serve the request path and optional an array of request parameters. Some requests require some
data in the request body - for these you can also give an instance of an implementation of a requestHandlers\RequestHandler.
These implementation prepares a certain data type for the http api request.

Here are some examples:
```
$response = $docker->get('/containers/json');
```

```
$response = $docker->head('/containers/4fa6e0f0c678/archive', ['path' => '/path/on/container']);
```

```
$response = $docker->post('/containers/create', [], new requestHandlers\Json(['Image' => '4fa6e0f0c678']));
```

```
$response = $docker->put(
	'/containers/4fa6e0f0c678/archive',
	['path' => '/path/on/container'],
	new requestHandlers\Files('/path/on/local/machine')
);
```

From the response you can get the status code, to decide what type of ResponseHandler you need.
Use a responseHandler\ResponseHandler to get informations from the response or to start actions on the local machine
like unpacking a tar archive.

Here are some examples:
```
$response = $docker->get('/containers/json');
if($response->getStatus() === 200) {
	$responseHandler = new responseHandlers\Json($response);
	$containers = $responseHandler->getData();
	var_dump($containers);
}
```

```
$response = $docker->get('/containers/4fa6e0f0c678/archive', ['path' => '/path/on/container']);
if($response->getStatus() === 200) {
	$responseHandler = new responseHandlers\Files($response);
	$stat = json_decode(base64_decode($responseHandler->getHeader('x-docker-container-path-stat')), true);
	var_dump($stat);
	$responseHandler->extract('/path/on/local/machine');
}
```

## Hint
Use the environment variable HOSTNAME as the container id of your own host.

# Developers
Tests are running inside a docker container. Install all the test dependencies by going to the "containers" folder
and type: `./docker-compose -f install.yml up`

Update dependencies with:
`./docker-compose -f update.yml up`

Run the tests with:
`./docker-compose up`

This will make the API available through the container
["jarkt/docker-remote-api"](https://github.com/jarkt/docker-remote-api) and run the tests against this endpoint.
