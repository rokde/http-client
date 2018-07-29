<?php

namespace Rokde\HttpClient;

class Request
{
	/**
	 * @var string protocol version
	 */
	protected $protocolVersion = '1.1';

	/**
	 * @var array|Header[] headers
	 */
	protected $headers = [];

	/**
	 * @var string http method
	 */
	protected $method;

	/**
	 * @var string|null content
	 */
	protected $content;

	/**
	 * @var Uri|null content
	 */
	protected $uri;

	public function __construct(string $url = null, string $method = 'GET', array $headers = [])
	{
		if ($url !== null) {
			$this->setUri($url);
		}

		$this->setMethod($method);

		foreach ($headers as $name => $value) {
			$this->setHeader($name, $value);
		}
	}

	/**
	 * Retrieves the HTTP protocol version as a string.
	 *
	 * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
	 *
	 * @return string HTTP protocol version.
	 */
	public function protocolVersion(): string
	{
		return $this->protocolVersion;
	}

	/**
	 * Return an instance with the specified HTTP protocol version.
	 *
	 * The version string MUST contain only the HTTP version number (e.g.,
	 * "1.1", "1.0").
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * new protocol version.
	 *
	 * @param string $version HTTP protocol version
	 * @return static
	 */
	public function setProtocolVersion(string $version): self
	{
		$this->protocolVersion = $version;

		return $this;
	}

	/**
	 * Retrieves all message header values.
	 *
	 * The keys represent the header name as it will be sent over the wire, and
	 * each value is an array of strings associated with the header.
	 *
	 *     // Represent the headers as a string
	 *     foreach ($message->getHeaders() as $name => $values) {
	 *         echo $name . ": " . implode(", ", $values);
	 *     }
	 *
	 *     // Emit headers iteratively:
	 *     foreach ($message->getHeaders() as $name => $values) {
	 *         foreach ($values as $value) {
	 *             header(sprintf('%s: %s', $name, $value), false);
	 *         }
	 *     }
	 *
	 * While header names are not case-sensitive, getHeaders() will preserve the
	 * exact case in which headers were originally specified.
	 *
	 * @return string[][]|Header[] Returns an associative array of the message's headers. Each
	 *     key MUST be a header name, and each value MUST be an array of strings
	 *     for that header.
	 */
	public function headers(): array
	{
		return $this->headers;
	}

	/**
	 * Checks if a header exists by the given case-insensitive name.
	 *
	 * @param string $name Case-insensitive header field name.
	 * @return bool Returns true if any header names match the given header
	 *     name using a case-insensitive string comparison. Returns false if
	 *     no matching header name is found in the message.
	 */
	public function hasHeader(string $name): bool
	{
		$name = $this->unifyHeaderName($name);

		return array_key_exists($name, $this->headers);
	}

	/**
	 * Retrieves a message header value by the given case-insensitive name.
	 *
	 * This method returns an array of all the header values of the given
	 * case-insensitive header name.
	 *
	 * If the header does not appear in the message, this method MUST return an
	 * empty array.
	 *
	 * @param string $name Case-insensitive header field name.
	 * @return string[] An array of string values as provided for the given
	 *    header. If the header does not appear in the message, this method MUST
	 *    return an empty array.
	 */
	public function header($name): ?array
	{
		$name = $this->unifyHeaderName($name);

		return $this->headers[$name] ?? null;
	}

	/**
	 * Return an instance with the provided value replacing the specified header.
	 *
	 * While header names are case-insensitive, the casing of the header will
	 * be preserved by this function, and returned from getHeaders().
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * new and/or updated header and value.
	 *
	 * @param string $name Case-insensitive header field name.
	 * @param string|string[] $value Header value(s).
	 * @return static
	 * @throws \InvalidArgumentException for invalid header names or values.
	 */
	public function setHeader(string $name, $value = null): self
	{
		if ($value === null) {
			// header strings given: e.g. "Content-Type: application/json"
			$header = Header::fromString($name);
		} else {
			$name = $this->unifyHeaderName($name);

			$header = array_key_exists($name, $this->headers)
				? $this->headers[$name]
				: new Header($name);

			$header->addValue($value);
		}

		$this->headers[$header->name()] = $header;

		return $this;
	}

	/**
	 * Return an instance without the specified header.
	 *
	 * Header resolution MUST be done without case-sensitivity.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that removes
	 * the named header.
	 *
	 * @param string $name Case-insensitive header field name to remove.
	 * @return static
	 */
	public function withoutHeader(string $name): self
	{
		$name = $this->unifyHeaderName($name);
		unset($this->headers[$name]);

		return $this;
	}

	/**
	 * Gets the body of the message.
	 *
	 * @return String|null Returns the body as a stream.
	 */
	public function body(): ?string
	{
		return $this->content;
	}

	/**
	 * Return an instance with the specified message body.
	 *
	 * The body MUST be a StreamInterface object.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return a new instance that has the
	 * new body stream.
	 *
	 * @param string|null $body Body.
	 * @return static
	 * @throws \InvalidArgumentException When the body is not valid.
	 */
	public function setBody(?string $body): self
	{
		$this->content = $body;

		return $this;
	}

	/**
	 * Retrieves the HTTP method of the request.
	 *
	 * @return string Returns the request method.
	 */
	public function method(): string
	{
		return $this->method;
	}

	/**
	 * Return an instance with the provided HTTP method.
	 *
	 * While HTTP method names are typically all uppercase characters, HTTP
	 * method names are case-sensitive and thus implementations SHOULD NOT
	 * modify the given string.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * changed request method.
	 *
	 * @param string $method Case-sensitive method.
	 * @return static
	 * @throws \InvalidArgumentException for invalid HTTP methods.
	 */
	public function setMethod(string $method): self
	{
		$this->method = strtoupper($method);

		return $this;
	}

	/**
	 * Retrieves the URI instance.
	 *
	 * This method MUST return a UriInterface instance.
	 *
	 * @link http://tools.ietf.org/html/rfc3986#section-4.3
	 * @return Uri Returns a UriInterface instance
	 *     representing the URI of the request.
	 */
	public function uri(): Uri
	{
		return $this->uri;
	}

	/**
	 * Returns an instance with the provided URI.
	 *
	 * This method MUST update the Host header of the returned request by
	 * default if the URI contains a host component. If the URI does not
	 * contain a host component, any pre-existing Host header MUST be carried
	 * over to the returned request.
	 *
	 * You can opt-in to preserving the original state of the Host header by
	 * setting `$preserveHost` to `true`. When `$preserveHost` is set to
	 * `true`, this method interacts with the Host header in the following ways:
	 *
	 * - If the Host header is missing or empty, and the new URI contains
	 *   a host component, this method MUST update the Host header in the returned
	 *   request.
	 * - If the Host header is missing or empty, and the new URI does not contain a
	 *   host component, this method MUST NOT update the Host header in the returned
	 *   request.
	 * - If a Host header is present and non-empty, this method MUST NOT update
	 *   the Host header in the returned request.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * new UriInterface instance.
	 *
	 * @link http://tools.ietf.org/html/rfc3986#section-4.3
	 * @param string|Uri $uri New request URI to use.
	 * @param bool $preserveHost Preserve the original state of the Host header.
	 * @return static
	 */
	public function setUri($uri, $preserveHost = false): self
	{
		if ($preserveHost && $this->uri instanceof Uri) {
			$currentUri = $this->uri;
			$uri = $uri instanceof Uri ? $uri : Uri::fromString($uri);

			$uri->withHost($currentUri->getHost());
			$this->uri = $uri;
		} else {
			$this->uri = $uri instanceof Uri ? $uri : Uri::fromString($uri);
		}

		return $this;
	}

	public function asJson(): self
	{
		return $this->setHeader('Content-Type', 'application/json');
	}

	public function asForm(): self
	{
		return $this->setHeader('Content-Type', 'application/x-www-form-urlencoded');
	}

	public function accept(string $contentType): self
	{
		return $this->setHeader('Accept', $contentType);
	}

	/**
	 * preset all settings for sending a get
	 *
	 * @param string|Uri $url
	 * @return Request
	 */
	public function get($url = null): self
	{
		if ($url !== null) {
			$this->setUri($url);
		}

		return $this->setMethod('GET');
	}

	/**
	 * preset all settings for sending a post
	 *
	 * @param array $data
	 * @param null|string|Uri $url
	 * @return Request
	 */
	public function post(array $data, $url = null): self
	{
		if ($url !== null) {
			$this->setUri($url);
		}

		return $this->presetFormBasedRequests('POST', $data);
	}

	/**
	 * preset all settings for sending a put
	 *
	 * @param array $data
	 * @param null|string|Uri $url
	 * @return Request
	 */
	public function put(array $data, $url = null): self
	{
		if ($url !== null) {
			$this->setUri($url);
		}

		return $this->presetFormBasedRequests('PUT', $data);
	}

	/**
	 * preset all settings for sending a patch
	 *
	 * @param array $data
	 * @param null|string|Uri $url
	 * @return Request
	 */
	public function patch(array $data, $url = null): self
	{
		if ($url !== null) {
			$this->setUri($url);
		}

		return $this->presetFormBasedRequests('PATCH', $data);
	}

	/**
	 * preset all settings for sending a delete
	 *
	 * @param null|string|Uri $url
	 * @param array|null $data
	 * @return Request
	 */
	public function delete($url = null, array $data = null): self
	{
		if ($url !== null) {
			$this->setUri($url);
		}

		if ($data !== null) {
			return $this->presetFormBasedRequests('DELETE', $data);
		}

		return $this->setMethod('DELETE')->setBody(null);
	}

	private function presetFormBasedRequests(string $method, array $data): self
	{
		return $this->setMethod($method)
			->asForm()
			->setBody(http_build_query($data));
	}

	/**
	 * unifies the header name for internal use
	 *
	 * @param string $name
	 * @return string
	 */
	private function unifyHeaderName(string $name): string
	{
		return strtolower($name);
	}
}