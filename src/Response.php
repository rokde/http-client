<?php


namespace Rokde\HttpClient;


class Response
{
	/**
	 * @var string response body
	 */
	private $content;

	/**
	 * @var array|Header[] header collection
	 */
	private $headers = [];

	/**
	 * @var int initial response status code
	 */
	private $status = 0;

	/**
	 * @var null|string inital response status message
	 */
	private $status_message = null;

	/**
	 * @var string initial protocal version
	 */
	private $protocolVersion;

	/**
	 * do not instantiate it directly
	 */
	private function __construct()
	{
	}

	/**
	 * factory creation of a response
	 *
	 * @param array $responseHeaderLines
	 * @param string $content
	 * @return Response
	 */
	public static function create(array $responseHeaderLines, string $content): self
	{
		$response = new static();
		$response->content = $content;

		foreach ($responseHeaderLines as $headerLine) {
			try {
				$header = Header::fromString($headerLine);
				$response->headers[$header->getName()] = $header;
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

	public function status(): int
	{
		return $this->status;
	}

	public function content(): string
	{
		return $this->content;
	}

	public function json()
	{
		if (!extension_loaded('json')) {
			throw new \RuntimeException('The necessary json extension is not loaded');
		}

		return json_decode($this->content(), true);
	}

	public function __toString(): string
	{
		$headers = '';
		/** @var Header $header */
		foreach ($this->headers as $header) {
			$headers .= $header->getValueLine();
		}

		return 'HTTP/' . $this->protocolVersion . ' ' . $this->status() . ' ' . $this->status_message . "\r\n"
			. $headers . "\r\n"
			. $this->content();
	}

	public function isOk(): bool
	{
		return $this->status() >= 200 && $this->status() < 300;
	}

	function isRedirect(): bool
	{
		return $this->status() >= 300 && $this->status() < 400;
	}

	function isClientError(): bool
	{
		return $this->status() >= 400 && $this->status() < 500;
	}

	function isServerError(): bool
	{
		return $this->status() >= 500;
	}
}