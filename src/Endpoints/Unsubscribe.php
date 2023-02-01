<?php

namespace ModishMailerSend\Endpoints;

use ModishMailerSend\Common\HttpLayer;

class Unsubscribe extends Suppression
{
    public function __construct(HttpLayer $httpLayer, array $options)
    {
        $endpoint = 'suppressions/unsubscribes';
        parent::__construct($httpLayer, $options, $endpoint);
    }
}
