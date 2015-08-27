<?php

namespace AdamPaterson\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Slack
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
        return "https://slack.com/oauth/authorize";
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
        return "https://slack.com/api/oauth.access";
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
        return "https://slack.com/api/team.info?token=".$token;
    }

    /**
     * Checks a provider response for errors.
     *
     * @throws IdentityProviderException
     *
     * @param  ResponseInterface $response
     * @param  array|string $data Parsed response data
     *
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (isset($data['ok']) && $data['ok'] == false) {
            $error = isset($error['error']) ? $error['error']: 'Unknown error';
            throw new IdentityProviderException($error, 400, $data);
        }
    }

    /**
     * Create new resources owner using the generated access token.
     *
     * @param array $response
     * @param AccessToken $token
     *
     * @return SlackTeamResourceOwner
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new SlackTeamResourceOwner($response, null);
    }

    /**
     * @return array
     */
    protected function getDefaultScopes()
    {
        return [];
    }
}
