<?php

class HeaderTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function it_can_resolve_a_header_from_string()
    {
        $header = \Rokde\HttpClient\Header::fromString('Date: Sun, 29 Jul 2018 09:58:18 GMT');

        $this->assertEquals('date', $header->name());
        $this->assertEquals('Sun, 29 Jul 2018 09:58:18 GMT', $header->value()[0]);
    }

    /** @test */
    public function it_can_make_a_string_representation_of_itself()
    {
        $header = new \Rokde\HttpClient\Header('DATE');
        $header->setValue('Sun, 29 Jul 2018 09:58:18 GMT');

        $this->assertEquals("date: Sun, 29 Jul 2018 09:58:18 GMT\r\n", (string)$header);
    }

    /** @test */
    public function it_can_not_resolve_http_version_header()
    {
        $this->expectException(InvalidArgumentException::class);

        \Rokde\HttpClient\Header::fromString('HTTP/1.1 200 OK');
    }
}
