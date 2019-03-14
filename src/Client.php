<?php

namespace Rokde\HttpClient;

class Client
{
    /**
     * @var ClientContext
     */
    private $context;

    /**
     * @param string|null $userAgent
     * @param \Rokde\HttpClient\ClientContext|null $context
     */
    public function __construct(string $userAgent = null, ClientContext $context = null)
    {
        $this->context = $context ?: new ClientContext();
        if ($userAgent !== null) {
            $this->context->setUserAgent($userAgent);
        }
    }

    public function context(): ClientContext
    {
        return $this->context;
    }

    /**
     * sending a request
     *
     * @param  Request $request
     * @return Response
     */
    public function send(Request $request): Response
    {
        $this->context->updateFromRequest($request);

        $content = $request->body();
        if ($content !== null || in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $this->context->setContent($content);
        }

        $fp = fopen((string)$request->uri(), 'r', false, $this->context->asContext());

        $result = '';
        while (!feof($fp)) {
            $result .= fread($fp, 512);
        }

        $metadata = @stream_get_meta_data($fp);
        fclose($fp);

        return Response::create($metadata['wrapper_data'], $result);
    }
}
