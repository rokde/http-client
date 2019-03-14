<?php

class HttpGetTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function it_can_fetch_an_url_via_get_method()
    {
        $client = new \Rokde\HttpClient\Client();

        $request = new \Rokde\HttpClient\Request('https://httpbin.org/get', 'GET', [
            'accept' => 'application/json',
            'x-verify-test' => 'true',
        ]);

        $response = $client->send($request);

        $this->assertEquals([
            'Accept' => 'application/json',
            'Host' => 'httpbin.org',
            'X-Verify-Test' => 'true',
            'User-Agent' => 'rokde-httpclient/1.1',
        ], $response->json()['headers']);
        $this->assertEquals('https://httpbin.org/get', $response->json()['url']);

        $this->assertTrue($response->isOk());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isClientError());
        $this->assertFalse($response->isServerError());
    }
}
