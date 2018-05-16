<?php

namespace AdamPaterson\OAuth2\Client\Provider;

use AdamPaterson\OAuth2\Client\Provider\Exception\TooManyRequestsException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;
use AdamPaterson\OAuth2\Client\Provider\Exception\SlackProviderException;

/**
 * Class Slack
 *
 * @author Adam Paterson <hello@adampaterson.co.uk>
 *
 * @package AdamPaterson\OAuth2\Client\Provider
 */
class Slack extends AbstractProvider
{
    /**
     * Returns the base URL for authorizing a client.
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return 'https://slack.com/oauth/authorize';
    }

    /**
     * Returns the base URL for requesting an access token.
     *
     * @param array $params
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://slack.com/api/oauth.access';
    }

    /**
     * Returns the URL for requesting the resource owner's details.
     *
     * @param AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        $authorizedUser = $this->getAuthorizedUser($token);

        $params = [
            'token' => $token->getToken(),
            'user'  => $authorizedUser->getId()
        ];

        return 'https://slack.com/api/users.info?'.http_build_query($params);
    }

    /**
     * @param $token
     *
     * @return string
     */
    public function getAuthorizedUserTestUrl($token)
    {
        return 'https://slack.com/api/auth.test?token=' . $token;
    }

    /**
     * Checks a provider response for errors.
     *
     * @param ResponseInterface $response
     * @param array|string      $data Parsed response data
     *
     * @return \AdamPaterson\OAuth2\Client\Provider\Slack
     * @throws \AdamPaterson\OAuth2\Client\Provider\Exception\SlackProviderException
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        $error = isset($data['ok']) && $data['ok'] === false;

        if (!$error) {
            return $this;
        }

        if ($error && $this->hasRateLimitExceeded($response)) {
            return TooManyRequestsException::fromResponse($response, $data['error']);
        }

        if (isset($data['ok']) && $data['ok'] === false) {
            return SlackProviderException::fromResponse($response, $data['error']);
        }
    }

    /**
     * Create new resources owner using the generated access token.
     *
     * @param array       $response
     * @param AccessToken $token
     *
     * @return SlackResourceOwner
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new SlackResourceOwner($response);
    }

    /**
     * @return array
     */
    protected function getDefaultScopes()
    {
        return [];
    }

    /**
     * @param AccessToken $token
     *
     * @return mixed
     */
    public function fetchAuthorizedUserDetails(AccessToken $token)
    {
        $url = $this->getAuthorizedUserTestUrl($token);

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);

        // Keep compatibility with League\OAuth2\Client v1
        if (!method_exists($this, 'getParsedResponse')) {
            return $this->getResponse($request);
        }

        return $this->getParsedResponse($request);
    }

    /**
     * @param AccessToken $token
     *
     * @return SlackAuthorizedUser
     */
    public function getAuthorizedUser(AccessToken $token)
    {
        $response = $this->fetchAuthorizedUserDetails($token);

        return $this->createAuthorizedUser($response);
    }

    /**
     * @param $response
     *
     * @return SlackAuthorizedUser
     */
    protected function createAuthorizedUser($response)
    {
        return new SlackAuthorizedUser($response);
    }

    /**
     * Response has Retry-After header?
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return bool
     */
    private function hasRateLimitExceeded(ResponseInterface $response)
    {
        return $response->hasHeader('retry-after');
    }
}
