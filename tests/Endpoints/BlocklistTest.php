<?php

namespace ModishMailerSend\Tests\Endpoints;

use Http\Mock\Client;
use ModishMailerSend\Common\Constants;
use ModishMailerSend\Common\HttpLayer;
use ModishMailerSend\Endpoints\Blocklist;
use ModishMailerSend\Exceptions\MailerSendAssertException;
use ModishMailerSend\Helpers\Builder\BlocklistParams;
use ModishMailerSend\Tests\TestCase;
use Psr\Http\Message\ResponseInterface;
use Tightenco\Collect\Support\Arr;

class BlocklistTest extends TestCase
{
    protected Blocklist $blocklist;
    protected ResponseInterface $defaultResponse;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = new Client();

        $this->blocklist = new Blocklist(new HttpLayer(self::OPTIONS, $this->client), self::OPTIONS);
        $this->defaultResponse = $this->createMock(ResponseInterface::class);
        $this->defaultResponse->method('getStatusCode')->willReturn(200);
    }

    /**
     * @dataProvider validGetAllDataProvider
     * @throws MailerSendAssertException
     * @throws \JsonException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function test_get_all(array $params): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);

        $this->client->addResponse($response);

        $response = $this->blocklist->getAll(
            Arr::get($params, 'domain_id'),
            Arr::get($params, 'page'),
            Arr::get($params, 'limit'),
        );

        $request = $this->client->getLastRequest();
        parse_str($request->getUri()->getQuery(), $query);

        self::assertEquals('GET', $request->getMethod());
        self::assertEquals('/v1/suppressions/blocklist', $request->getUri()->getPath());
        self::assertEquals(200, $response['status_code']);
        self::assertEquals(Arr::get($params, 'domain_id'), Arr::get($query, 'domain_id'));
        self::assertEquals(Arr::get($params, 'page'), Arr::get($query, 'page'));
        self::assertEquals(Arr::get($params, 'limit'), Arr::get($query, 'limit'));
    }

    /**
     * @dataProvider invalidGetAllDataProvider
     * @throws MailerSendAssertException
     * @throws \JsonException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function test_get_all_with_errors(array $params, string $errorMessage): void
    {
        $this->expectException(MailerSendAssertException::class);
        $this->expectExceptionMessage($errorMessage);

        $this->blocklist->getAll(
            Arr::get($params, 'domain_id'),
            Arr::get($params, 'page'),
            Arr::get($params, 'limit'),
        );
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     * @throws MailerSendAssertException
     */
    public function test_create(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);

        $this->client->addResponse($response);

        $response = $this->blocklist->create(
            (new BlocklistParams())
                ->setDomainId('domain_id')
                ->setRecipients([
                    'recipient'
                ])
                ->setPatterns([
                    'pattern'
                ])
        );

        $request = $this->client->getLastRequest();
        $request_body = json_decode((string)$request->getBody(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals('POST', $request->getMethod());
        self::assertEquals('/v1/suppressions/blocklist', $request->getUri()->getPath());
        self::assertEquals(200, $response['status_code']);
        self::assertSame('domain_id', Arr::get($request_body, 'domain_id'));
        self::assertSame(['recipient'], Arr::get($request_body, 'recipients'));
        self::assertSame(['pattern'], Arr::get($request_body, 'patterns'));
    }

    public function test_create_requires_either_recipients_or_patterns(): void
    {
        $this->expectException(MailerSendAssertException::class);
        $this->expectExceptionMessage('Either recipients or patterns must be provided.');

        $this->blocklist->create(
            (new BlocklistParams())
                ->setDomainId('domain_id')
        );
    }

    /**
     * @dataProvider validDeleteDataProvider
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     * @throws MailerSendAssertException
     */
    public function test_delete(array $params): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);

        $this->client->addResponse($response);

        $response = $this->blocklist->delete(
            Arr::get($params, 'ids'),
            Arr::get($params, 'all', false),
            Arr::get($params, 'domain_id'),
        );

        $request = $this->client->getLastRequest();
        $request_body = json_decode((string)$request->getBody(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals('DELETE', $request->getMethod());
        self::assertEquals('/v1/suppressions/blocklist', $request->getUri()->getPath());
        self::assertEquals(200, $response['status_code']);
        self::assertSame(Arr::get($params, 'ids'), Arr::get($request_body, 'ids'));
        self::assertSame(Arr::get($params, 'all', false), Arr::get($request_body, 'all'));
        self::assertSame(Arr::get($params, 'domain_id'), Arr::get($request_body, 'domain_id'));
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     */
    public function test_delete_requires_either_ids_or_all(): void
    {
        $this->expectException(MailerSendAssertException::class);
        $this->expectExceptionMessage('Either ids or all must be provided.');

        $this->blocklist->delete();
    }

    public function validGetAllDataProvider(): array
    {
        return [
            'empty request' => [
                'params' => [],
            ],
            'with domain id' => [
                'params' => [
                    'domain_id' => 'domain_id',
                ],
            ],
            'with limit' => [
                'params' => [
                    'limit' => 10,
                ],
            ],
            'with page' => [
                'params' => [
                    'page' => 1,
                ],
            ],
            'complete request' => [
                'params' => [
                    'domain_id' => 'domain_id',
                    'page' => 1,
                    'limit' => 10,
                ],
            ]
        ];
    }

    public function invalidGetAllDataProvider(): array
    {
        return [
            'with limit under 10' => [
                'params' => [
                    'domain_id' => 'domain_id',
                    'limit' => 9,
                ],
                'errorMessage' => 'Limit is supposed to be between ' . Constants::MIN_LIMIT . ' and ' . Constants::MAX_LIMIT .  '.',
            ],
            'with limit over 100' => [
                'params' => [
                    'domain_id' => 'domain_id',
                    'limit' => 101,
                ],
                'errorMessage' => 'Limit is supposed to be between ' . Constants::MIN_LIMIT . ' and ' . Constants::MAX_LIMIT .  '.',
            ],
        ];
    }

    public function validDeleteDataProvider(): array
    {
        return [
            'with ids' => [
                'params' => [
                    'ids' => ['id']
                ],
            ],
            'all' => [
                'params' => [
                    'all' => true,
                ],
            ],
            'with domain id' => [
                'params' => [
                    'ids' => ['id'],
                    'domain_id' => 'domain_id',
                ]
            ]
        ];
    }
}
