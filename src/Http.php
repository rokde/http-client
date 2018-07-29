<?php


namespace Rokde\HttpClient;

/**
 * Class Http
 * @method string getProtocolVersion()
 * @method Request withProtocolVersion(string $version)
 * @method array getHeaders()
 * @method bool hasHeader(string $name)
 * @method ?array getHeader($name)
 * @method Request withHeader(string $name, $value = null)
 * @method Request withoutHeader(string $name)
 * @method ?string getBody()
 * @method Request withBody(?string $body)
 * @method string getMethod()
 * @method Request withMethod(string $method)
 * @method Uri getUri()
 * @method Request withUri($uri, $preserveHost = false)
 * @method Request asJson()
 * @method Request asForm()
 * @method Request accept()
 * @method Request get($url)
 * @package Rokde\HttpClient
 */
class Http
{
	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * @var Client
	 */
	protected $client;

	public function __construct(string $url = null, string $method = 'GET', array $headers = [])
	{
		$this->request = new Request($url, $method, $headers);
		$this->client = new Client();
	}

	public function send(): Response
	{
		return $this->client->send($this->request);
	}

	/**
	 * route every method call to the request instance
	 *
	 * @param string $name
	 * @param array $arguments
	 * @return mixed
	 */
	public function __call($name, $arguments)
	{
		return call_user_func_array([$this->request, $name], $arguments);
	}
}