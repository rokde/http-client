<?php


class UriTest extends \PHPUnit\Framework\TestCase
{
	/** @test */
	public function it_can_make_an_uri_from_string()
	{
		$url = 'https://max:mustermann@www.testdomain.com:8081/path/to/my/site?debug=1#comments-21';

		$uri = \Rokde\HttpClient\Uri::fromString($url);

		$this->assertEquals('https', $uri->scheme());
		$this->assertEquals('max:mustermann', $uri->userInfo());
		$this->assertEquals('www.testdomain.com', $uri->host());
		$this->assertEquals(8081, $uri->port());
		$this->assertEquals('/path/to/my/site', $uri->path());
		$this->assertEquals('debug=1', $uri->query());
		$this->assertEquals('comments-21', $uri->fragment());
		$this->assertEquals($url, $uri->__toString());
	}

	/** @test */
	public function it_can_reduce_output_by_using_default_values()
	{
		$url = 'https://www.testdomain.com';

		$uri = \Rokde\HttpClient\Uri::fromString($url);

		$this->assertEquals('https', $uri->scheme());
		$this->assertEquals('', $uri->userInfo());
		$this->assertEquals('www.testdomain.com', $uri->host());
		$this->assertEquals(443, $uri->port());
		$this->assertEquals('/', $uri->path());
		$this->assertEquals($url . '/', $uri->__toString());
	}
}