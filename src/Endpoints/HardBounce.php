<?php

namespace ModishMailerSend\Endpoints;

use ModishMailerSend\Common\HttpLayer;

class HardBounce extends Suppression
{
    public function __construct(HttpLayer $httpLayer, array $options)
    {
        $endpoint = 'suppressions/hard-bounces';
        parent::__construct($httpLayer, $options, $endpoint);
    }
}
