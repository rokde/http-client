<?php

namespace Rokde\HttpClient;

class Client
{
    /**
     * @var ClientContext
     */
    private $context;

    /**
     * @var string user agent
     */
    private $user_agent;

    /**
     * @param string|null $userAgent
     */
    public function __construct(string $userAgent = null)
    {
        $this->user_agent = $userAgent ?: 'rokde-httpclient/1.1';
    }

    /**
     * sending a request
     *
     * @param  Request $request
     * @return Response
     */
    public function send(Request $request): Response
    {
        $this->context = ClientContext::createFromRequest($request)
            ->setUserAgent($this->user_agent);

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
