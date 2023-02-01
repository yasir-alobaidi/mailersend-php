<?php

namespace ModishMailerSend\Endpoints;

use ModishMailerSend\Common\HttpLayer;
use ModishMailerSend\Helpers\BuildUri;
use ModishMailerSend\Helpers\Url;

abstract class AbstractEndpoint
{
    protected HttpLayer $httpLayer;
    protected array $options;

    public function __construct(HttpLayer $httpLayer, array $options)
    {
        $this->httpLayer = $httpLayer;
        $this->options = $options;
    }

    protected function buildUri(string $path, array $params = []): string
    {
        return (new BuildUri($this->options))->execute($path, $params);
    }

    protected function url(string $path, array $params = []): string
    {
        return (new Url($this->options))->execute($path, $params);
    }
}
