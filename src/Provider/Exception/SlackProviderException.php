<?php

namespace AdamPaterson\OAuth2\Client\Provider\Exception;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Http\Message\ResponseInterface;

class SlackProviderException extends IdentityProviderException
{
    /**
     * @param  ResponseInterface $response
     * @param  string|null       $message
     *
     * @throws \AdamPaterson\OAuth2\Client\Provider\Exception\SlackProviderException
     */
    public static function fromResponse(ResponseInterface $response, $message = null)
    {
        throw new static($message, $response->getStatusCode(), (string) $response->getBody());
    }
}
