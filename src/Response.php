<?php

namespace Rokde\HttpClient;

class Response
{
    /** @var string response body */
    private $content;

    /** @var array|Header[] header collection */
    private $headers = [];

    /** @var int initial response status code */
    private $status = 0;

    /** @var null|string inital response status message */
    private $status_message = null;

    /** @var string initial protocal version */
    private $protocolVersion;

    /** do not instantiate it directly */
    private function __construct()
    {
    }

    public static function create(array $responseHeaderLines, string $content): self
    {
        $response = new static();
        $response->content = $content;

        foreach ($responseHeaderLines as $headerLine) {
            try {
                $header = Header::fromString($headerLine);
                $response->headers[$header->name()] = $header;
            } catch (\InvalidArgumentException $e) {
                if (preg_match('~^HTTP\/([\d\.]*)\ (\d{3})\ (.*)$~', $headerLine, $matches)) {
                    $response->protocolVersion = $matches[1];
                    $response->status = $matches[2] + 0;
                    $response->status_message = $matches[3];
                }
            }
        }

        return $response;
    }

    public function json()
    {
        return json_decode($this->content(), true);
    }

    public function content(): ?string
    {
        return $this->content;
    }

    public function __toString(): string
    {
        $headers = '';
        /** @var Header $header */
        foreach ($this->headers as $header) {
            $headers .= $header->valueLine();
        }

        return 'HTTP/' . $this->protocolVersion . ' ' . $this->status() . ' ' . $this->status_message . "\r\n"
            . $headers . "\r\n"
            . $this->content();
    }

    public function status(): int
    {
        return $this->status;
    }

    public function isOk(): bool
    {
        return $this->status() >= 200 && $this->status() < 300;
    }

    public function isRedirect(): bool
    {
        return $this->status() >= 300 && $this->status() < 400;
    }

    public function isClientError(): bool
    {
        return $this->status() >= 400 && $this->status() < 500;
    }

    public function isServerError(): bool
    {
        return $this->status() >= 500;
    }

    /**
     * @return array|Header[] header collection
     */
    public function headers(): array
    {
        return $this->headers;
    }

    public function header(string $name, $default = null)
    {
        return array_key_exists($name, $this->headers) ? $this->headers[$name] : $default;
    }
}
