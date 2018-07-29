<?php


class HttpPostTest extends \PHPUnit\Framework\TestCase
{
	/** @test */
	public function it_can_post_form_data()
	{
		$client = new \Rokde\HttpClient\Client();

		$request = new \Rokde\HttpClient\Request('https://httpbin.org/post', 'POST');
		$request->asForm()
			->withBody(http_build_query([
				'input1' => 'value1'
			]));

		$response = $client->send($request);

		$this->assertArraySubset([
			'form' => [
				'input1' => 'value1',
			],
			'files' => [],
			'data' => '',
		], $response->json());

		$this->assertTrue($response->isOk());
		$this->assertFalse($response->isRedirect());
		$this->assertFalse($response->isClientError());
		$this->assertFalse($response->isServerError());
	}
}