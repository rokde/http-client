<?php

class HttpPatchTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function it_can_patch_form_data()
    {
        $client = new \Rokde\HttpClient\Client();

        $request = new \Rokde\HttpClient\Request('https://httpbin.org/patch', 'PATCH');
        $request->asForm()
            ->setBody(http_build_query([
                'input' => 'value',
            ]));

        $response = $client->send($request);

        $this->assertEquals([
            'input' => 'value',
        ], $response->json()['form']);
        $this->assertEquals([], $response->json()['files']);
        $this->assertEquals('', $response->json()['data']);

        $this->assertTrue($response->isOk());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isClientError());
        $this->assertFalse($response->isServerError());
    }
}
