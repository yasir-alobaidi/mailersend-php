<?php

namespace ModishMailerSend\Endpoints;

use Assert\Assertion;
use ModishMailerSend\Helpers\Builder\Attachment;
use ModishMailerSend\Helpers\Builder\EmailParams;
use ModishMailerSend\Helpers\Builder\Personalization;
use ModishMailerSend\Helpers\Builder\Recipient;
use ModishMailerSend\Helpers\Builder\SmsParams;
use ModishMailerSend\Helpers\Builder\SmsPersonalization;
use ModishMailerSend\Helpers\Builder\Variable;
use ModishMailerSend\Helpers\GeneralHelpers;
use Tightenco\Collect\Support\Collection;

class Sms extends AbstractEndpoint
{
    protected string $endpoint = 'sms';

    /**
     * @throws \JsonException
     * @throws \ModishMailerSend\Exceptions\MailerSendAssertException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function send(SmsParams $params): array
    {
        GeneralHelpers::validateSmsParams($params);

        $personalization_mapped = GeneralHelpers::mapToArray($params->getPersonalization(), SmsPersonalization::class);

        return $this->httpLayer->post(
            $this->url($this->endpoint),
            array_filter(
                [
                    'from' => $params->getFrom(),
                    'to' => $params->getTo(),
                    'text' => $params->getText(),
                    'personalization' => $personalization_mapped,
                ],
                fn ($v) => is_array($v) ? array_filter($v, fn ($v) => $v !== null) : $v !== null
            )
        );
    }
}
