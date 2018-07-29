<?php


class UriTest extends \PHPUnit\Framework\TestCase
{
	/** @test */
	public function it_can_make_an_uri_from_string()
	{
		$url = 'https://max:mustermann@www.testdomain.com:8081/path/to/my/site?debug=1#comments-21';

		$uri = \Rokde\HttpClient\Uri::fromString($url);

		$this->assertEquals('https', $uri->getScheme());
		$this->assertEquals('max:mustermann', $uri->getUserInfo());
		$this->assertEquals('www.testdomain.com', $uri->getHost());
		$this->assertEquals(8081, $uri->getPort());
		$this->assertEquals('/path/to/my/site', $uri->getPath());
		$this->assertEquals('debug=1', $uri->getQuery());
		$this->assertEquals('comments-21', $uri->getFragment());
		$this->assertEquals($url, $uri->__toString());
	}

	/** @test */
	public function it_can_reduce_output_by_using_default_values()
	{
		$url = 'https://www.testdomain.com';

		$uri = \Rokde\HttpClient\Uri::fromString($url);

		$this->assertEquals('https', $uri->getScheme());
		$this->assertEquals('', $uri->getUserInfo());
		$this->assertEquals('www.testdomain.com', $uri->getHost());
		$this->assertEquals(443, $uri->getPort());
		$this->assertEquals('/', $uri->getPath());
		$this->assertEquals($url . '/', $uri->__toString());
	}
}