<?php

namespace ModishMailerSend\Tests;

use ModishMailerSend\Endpoints\Email;
use ModishMailerSend\Exceptions\MailerSendException;
use ModishMailerSend\ModishMailerSend;

class MailerSendTest extends TestCase
{
    public function test_should_fail_without_api_key(): void
    {
        $this->expectException(MailerSendException::class);

        new ModishMailerSend();
    }

    public function test_should_have_email_endpoint_set(): void
    {
        $sdk = new ModishMailerSend([
            'api_key' => 'test'
        ]);

        self::assertInstanceOf(Email::class, $sdk->email);
    }
}
