<?php

class HttpPostTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function it_can_post_form_data()
    {
        $client = new \Rokde\HttpClient\Client();

        $request = new \Rokde\HttpClient\Request('https://httpbin.org/post', 'POST');
        $request->asForm()
            ->setBody(http_build_query([
                'input1' => 'value1',
            ]));

        $response = $client->send($request);

        $this->assertEquals([
            'input1' => 'value1',
        ], $response->json()['form']);
        $this->assertEquals([], $response->json()['files']);
        $this->assertEquals('', $response->json()['data']);

        $this->assertTrue($response->isOk());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isClientError());
        $this->assertFalse($response->isServerError());
    }
}
