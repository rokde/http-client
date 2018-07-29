<?php


class HelperTest extends \PHPUnit\Framework\TestCase
{
	/** @test */
	public function it_can_use_the_helper_function()
	{
		$response = http('https://httpbin.org/get');

		$this->assertInstanceOf(\Rokde\HttpClient\Response::class, $response);
	}

	/** @test */
	public function it_can_use_the_helper_function_for_more_complex_examples()
	{
		$http = http();
		$http->get(\Rokde\HttpClient\Uri::fromString('https://httpbin.org/get'))
			->asJson();

		$this->assertInstanceOf(\Rokde\HttpClient\Http::class, $http);
		$response = $http->send();

		$this->assertInstanceOf(\Rokde\HttpClient\Response::class, $response);
	}
}