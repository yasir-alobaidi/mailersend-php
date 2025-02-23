<?php

namespace ModishMailerSend\Endpoints;

use Assert\Assertion;
use ModishMailerSend\Common\Constants;
use ModishMailerSend\Helpers\GeneralHelpers;

class SmsMessage extends AbstractEndpoint
{
    protected string $endpoint = 'sms-messages';

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \ModishMailerSend\Exceptions\MailerSendAssertException
     * @throws \JsonException
     */
    public function getAll(?int $page = null, ?int $limit = Constants::DEFAULT_LIMIT): array
    {
        if ($limit) {
            GeneralHelpers::assert(
                fn () => Assertion::range(
                    $limit,
                    Constants::MIN_LIMIT,
                    Constants::MAX_LIMIT,
                    'Limit is supposed to be between ' . Constants::MIN_LIMIT . ' and ' . Constants::MAX_LIMIT .  '.'
                )
            );
        }

        return $this->httpLayer->get(
            $this->url($this->endpoint, [
                'page' => $page,
                'limit' => $limit,
            ])
        );
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     * @throws \ModishMailerSend\Exceptions\MailerSendAssertException
     */
    public function find(string $smsMessageId): array
    {
        GeneralHelpers::assert(
            fn () => Assertion::minLength($smsMessageId, 1, 'SMS message id is required.')
        );

        return $this->httpLayer->get(
            $this->url("$this->endpoint/$smsMessageId")
        );
    }
}
