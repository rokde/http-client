<?php

namespace Rokde\HttpClient;

class ClientContext
{
    /** @var string method */
    private $method = 'GET';

    /** @var array|Header[] headers collection */
    private $headers = [];

    /** @var string initial user agent */
    private $user_agent = 'rokde-httpclient/1.1';

    /** @var string|null initial content */
    private $content;

    /** @var string|null proxy usage */
    private $proxy;

    /** @var bool requesting a full uri or not */
    private $request_fulluri = false;

    /** @var int follow this number of location responses */
    private $follow_location = 0;

    /** @var int follow this number of redirects in the responses */
    private $max_redirects = 0;

    /** @var string protocol version */
    private $protocol_version = '1.1';

    /** @var float timeout in seconds */
    private $timeout = 1.0;

    /** @var bool ignore errors on sending and retrieving */
    private $ignore_errors = true;

    public static function createFromRequest(Request $request): self
    {
        return (new static())
            ->updateFromRequest($request);
    }

    public function updateFromRequest(Request $request): self
    {
        if ($request->basicAuth() !== null) {
            $request->withoutHeader('Authorization')
                ->setHeader('Authorization', 'Basic ' . base64_encode($request->basicAuth()));
        }
        if ($request->bearerToken() !== null) {
            $request->withoutHeader('Authorization')
                ->setHeader('Authorization', 'Bearer ' . $request->bearerToken());
        }

        $this->setMethod($request->method())
            ->setHeaders($request->headers())
            ->setProtocolVersion($request->protocolVersion())
            ->setTimeout($request->timeout());

        return $this;
    }

    public function setMethod(string $method): self
    {
        $this->method = strtoupper($method);

        return $this;
    }

    /**
     * @param  array|Header[] $headers
     * @return ClientContext
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    public function setUserAgent(string $user_agent): self
    {
        $this->user_agent = $user_agent;

        return $this;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function setProxy(?string $proxy): self
    {
        $this->proxy = $proxy;

        return $this;
    }

    public function requestFullUri(bool $requestFullUri = true): self
    {
        $this->request_fulluri = $requestFullUri === true;

        return $this;
    }

    public function disableFollowLocations(): self
    {
        return $this->followLocations(0);
    }

    public function followLocations(int $followLocationCount): self
    {
        $this->follow_location = $followLocationCount;

        return $this;
    }

    public function maxRedirects(int $maxRedirectCount): self
    {
        $this->max_redirects = $maxRedirectCount;

        return $this;
    }

    public function setProtocolVersion(string $protocolVersion): self
    {
        $this->protocol_version = $protocolVersion;

        return $this;
    }

    public function setTimeout(float $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function ignoreErrors(bool $ignoreErrors = true): self
    {
        $this->ignore_errors = $ignoreErrors === true;

        return $this;
    }

    /** @return resource */
    public function asContext()
    {
        return stream_context_create($this->asContextOptions());
    }

    public function asContextOptions(): array
    {
        $options = [
            'http' => [
                'method' => $this->method,
                'header' => $this->getHeaderLine($this->headers),
                'user_agent' => $this->user_agent,
                'content' => $this->content,
                'request_fulluri' => $this->request_fulluri,
                'follow_location' => $this->follow_location,
                'max_redirects' => $this->max_redirects,
                'protocol_version' => $this->protocol_version,
                'timeout' => $this->timeout,
                'ignore_errors' => $this->ignore_errors,
            ],
        ];

        if ($this->proxy !== null) {
            $options['http']['proxy'] = $this->proxy;
        }

        return $options;
    }

    private function getHeaderLine(array $headers = []): string
    {
        $line = '';

        /** @var Header $header */
        foreach ($headers as $header) {
            $line .= $header->valueLine();
        }

        return $line;
    }
}
