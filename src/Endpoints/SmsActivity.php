<?php

namespace ModishMailerSend\Endpoints;

use Assert\Assertion;
use ModishMailerSend\Common\Constants;
use ModishMailerSend\Helpers\Builder\SmsActivityParams;
use ModishMailerSend\Helpers\GeneralHelpers;

class SmsActivity extends AbstractEndpoint
{
    protected string $endpoint = 'sms-activity';

    /**
     * @throws \JsonException
     * @throws \ModishMailerSend\Exceptions\MailerSendAssertException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function getAll(SmsActivityParams $smsActivityParams): array
    {
        if ($smsActivityParams->getSmsNumberId()) {
            GeneralHelpers::assert(
                fn () => Assertion::minLength($smsActivityParams->getSmsNumberId(), 1, 'Sms number id is wrong.')
            );
        }


        if ($smsActivityParams->getLimit()) {
            GeneralHelpers::assert(
                fn () => Assertion::range(
                    $smsActivityParams->getLimit(),
                    Constants::MIN_LIMIT,
                    Constants::MAX_LIMIT,
                    'Limit is supposed to be between' . Constants::MIN_LIMIT . ' and ' . Constants::MAX_LIMIT . '.'
                )
            );
        }

        if ($smsActivityParams->getDateFrom() && $smsActivityParams->getDateTo()) {
            GeneralHelpers::assert(
                fn () => Assertion::greaterThan($smsActivityParams->getDateTo(), $smsActivityParams->getDateFrom())
            );
        }

        if (! empty($smsActivityParams->getStatus())) {
            $diff = array_diff($smsActivityParams->getStatus(), Constants::POSSIBLE_SMS_STATUSES);
            GeneralHelpers::assert(
                fn () => Assertion::count($diff, 0, 'The following statuses are invalid: ' . implode(', ', $diff))
            );
        }

        return $this->httpLayer->get($this->url("$this->endpoint", $smsActivityParams->toArray()));
    }
}
