<?php

namespace ModishMailerSend\Endpoints;

use Assert\Assertion;
use ModishMailerSend\Common\Constants;
use ModishMailerSend\Helpers\Builder\SenderIdentity as SenderIdentityBuilder;
use ModishMailerSend\Helpers\GeneralHelpers;

class SenderIdentity extends AbstractEndpoint
{
    protected string $endpoint = 'identities';

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     * @throws \ModishMailerSend\Exceptions\MailerSendAssertException
     */
    public function getAll(?string $domainId = null, ?int $page = null, ?int $limit = Constants::DEFAULT_LIMIT): array
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
            $this->buildUri($this->endpoint, [
                'domain_id' => $domainId,
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
    public function find(string $identityId): array
    {
        GeneralHelpers::assert(
            fn () => Assertion::minLength($identityId, 1, 'Sender identity id is required.')
        );

        return $this->httpLayer->get(
            $this->buildUri("$this->endpoint/$identityId")
        );
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     */
    public function create(SenderIdentityBuilder $params): array
    {
        return $this->httpLayer->post(
            $this->buildUri($this->endpoint),
            $params->toArray(),
        );
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     */
    public function update(string $identityId, SenderIdentityBuilder $params): array
    {
        return $this->httpLayer->put(
            $this->buildUri("$this->endpoint/$identityId"),
            $params->toArray(),
        );
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     * @throws \ModishMailerSend\Exceptions\MailerSendAssertException
     */
    public function delete(string $identityId): array
    {
        GeneralHelpers::assert(
            fn () => Assertion::minLength($identityId, 1, 'Sender identity id is required.')
        );

        return $this->httpLayer->delete(
            $this->buildUri("$this->endpoint/$identityId")
        );
    }
}
