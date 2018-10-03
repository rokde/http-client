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

        $this->assertArraySubset([
            'headers' => [
                'Accept' => 'application/json',
                'Connection' => 'close',
                'Host' => 'httpbin.org',
                'X-Verify-Test' => 'true',
            ],
            'url' => 'https://httpbin.org/get',
        ], $response->json());

        $this->assertTrue($response->isOk());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isClientError());
        $this->assertFalse($response->isServerError());
    }
}
