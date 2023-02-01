<?php

namespace ModishMailerSend\Helpers;

use Http\Client\Common\Plugin;
use Http\Promise\Promise;
use ModishMailerSend\Exceptions\MailerSendHttpException;
use ModishMailerSend\Exceptions\MailerSendValidationException;
use ModishMailerSend\Exceptions\MailerSendRateLimitException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpErrorHelper implements Plugin
{
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        $promise = $next($request);

        return $promise->then(function (ResponseInterface $response) use ($request) {
            $code = $response->getStatusCode();

            if ($code >= 200 && $code < 400) {
                return $response;
            }

            if ($code === 422) {
                throw new MailerSendValidationException($request, $response);
            }

            if ($code === 429) {
                throw new MailerSendRateLimitException($request, $response);
            }

            throw new MailerSendHttpException($request, $response);
        });
    }
}
