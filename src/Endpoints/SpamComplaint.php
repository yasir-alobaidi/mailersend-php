<?php

namespace ModishMailerSend\Endpoints;

use ModishMailerSend\Common\HttpLayer;

class SpamComplaint extends Suppression
{
    public function __construct(HttpLayer $httpLayer, array $options)
    {
        $endpoint = 'suppressions/spam-complaints';
        parent::__construct($httpLayer, $options, $endpoint);
    }
}
