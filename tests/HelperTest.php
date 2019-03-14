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
        $http->asJson()
            ->get(\Rokde\HttpClient\Uri::fromString('https://httpbin.org/get'));

        $this->assertInstanceOf(\Rokde\HttpClient\Http::class, $http);
        $response = $http->send();

        $this->assertInstanceOf(\Rokde\HttpClient\Response::class, $response);
    }

    /** @test */
    public function it_can_post_data()
    {
        $response = http()->post(['input' => 'value'], 'https://httpbin.org/post');

        $this->assertInstanceOf(\Rokde\HttpClient\Response::class, $response);

        $this->assertEquals([
            'input' => 'value',
        ], $response->json()['form']);
        $this->assertEquals([], $response->json()['files']);
        $this->assertEquals('', $response->json()['data']);
    }

    /** @test */
    public function it_can_put_data()
    {
        $response = http()->put(['input' => 'value'], 'https://httpbin.org/put');

        $this->assertInstanceOf(\Rokde\HttpClient\Response::class, $response);

        $this->assertEquals([
            'input' => 'value',
        ], $response->json()['form']);
        $this->assertEquals([], $response->json()['files']);
        $this->assertEquals('', $response->json()['data']);
    }

    /** @test */
    public function it_can_patch_data()
    {
        $response = http()->patch(['input' => 'value'], 'https://httpbin.org/patch');

        $this->assertInstanceOf(\Rokde\HttpClient\Response::class, $response);

        $this->assertEquals([
            'input' => 'value',
        ], $response->json()['form']);
        $this->assertEquals([], $response->json()['files']);
        $this->assertEquals('', $response->json()['data']);
    }

    /** @test */
    public function it_can_delete_data()
    {
        $response = http()->delete('https://httpbin.org/delete', ['input' => 'value']);

        $this->assertInstanceOf(\Rokde\HttpClient\Response::class, $response);

        $this->assertEquals([
            'input' => 'value',
        ], $response->json()['form']);
        $this->assertEquals([], $response->json()['files']);
        $this->assertEquals('', $response->json()['data']);
    }

    /** @test */
    public function it_can_delete_data_without_data()
    {
        $response = http()->delete('https://httpbin.org/delete');

        $this->assertInstanceOf(\Rokde\HttpClient\Response::class, $response);

        $this->assertEquals([], $response->json()['form']);
        $this->assertEquals([], $response->json()['files']);
        $this->assertEquals('', $response->json()['data']);
    }
}
