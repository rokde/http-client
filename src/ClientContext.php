<?php


namespace Rokde\HttpClient;


class ClientContext
{
	/**
	 * @var string method
	 */
	protected $method = 'GET';

	protected $headers = [];

	protected $user_agent = 'rokde-httpclient/1.0';

	protected $content;

	/**
	 * @var string|null proxy usage
	 */
	protected $proxy;

	protected $request_fulluri = false;

	protected $follow_location = 0;

	protected $max_redirects = 0;

	protected $protocol_version = '1.1';

	protected $timeout = 1.0;

	protected $ignore_errors = true;

	private function __construct()
	{
	}

	public static function createFromRequest(Request $request): self
	{
		$context = new static();
		$context->method = $request->getMethod();
		$context->headers = $request->getHeaders();
		$context->protocol_version = $request->getProtocolVersion();

		return $context;
	}

	/**
	 * @param string $method
	 * @return ClientContext
	 */
	public function setMethod(string $method): self
	{
		$this->method = strtoupper($method);

		return $this;
	}

	/**
	 * @param array|Header[] $headers
	 * @return ClientContext
	 */
	public function setHeaders(array $headers): self
	{
		$this->headers = $headers;

		return $this;
	}

	/**
	 * @param string $user_agent
	 * @return ClientContext
	 */
	public function setUserAgent(string $user_agent): self
	{
		$this->user_agent = $user_agent;

		return $this;
	}

	/**
	 * @param null|string $content
	 * @return ClientContext
	 */
	public function setContent(?string $content): self
	{
		$this->content = $content;

		return $this;
	}

	/**
	 * @param null|string $proxy
	 * @return ClientContext
	 */
	public function setProxy(?string $proxy): self
	{
		$this->proxy = $proxy;

		return $this;
	}

	/**
	 * @param bool $request_fulluri
	 * @return ClientContext
	 */
	public function requestFullUri(bool $request_fulluri = true): self
	{
		$this->request_fulluri = $request_fulluri === true;

		return $this;
	}

	/**
	 * @param int $follow_location
	 * @return ClientContext
	 */
	public function followLocations(int $follow_location): self
	{
		$this->follow_location = $follow_location;

		return $this;
	}

	public function disableFollowLocations(): self
	{
		return $this->followLocations(0);
	}

	/**
	 * @param int $max_redirects
	 * @return ClientContext
	 */
	public function maxRedirects(int $max_redirects): self
	{
		$this->max_redirects = $max_redirects;

		return $this;
	}

	/**
	 * @param string $protocol_version
	 * @return ClientContext
	 */
	public function setProtocolVersion(string $protocol_version): self
	{
		$this->protocol_version = $protocol_version;

		return $this;
	}

	/**
	 * @param float $timeout
	 * @return ClientContext
	 */
	public function setTimeout(float $timeout): self
	{
		$this->timeout = $timeout;

		return $this;
	}

	/**
	 * @param bool $ignore_errors
	 * @return ClientContext
	 */
	public function ignoreErrors(bool $ignore_errors = true): self
	{
		$this->ignore_errors = $ignore_errors === true;

		return $this;
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

	/**
	 * @return resource
	 */
	public function asContext()
	{
		return stream_context_create($this->asContextOptions());
	}

	private function getHeaderLine(array $headers = []): string
	{
		$headerline = '';

		/** @var Header $header */
		foreach ($headers as $header) {
			$headerline .= $header->getValueLine();
		}

		return $headerline;
	}
}