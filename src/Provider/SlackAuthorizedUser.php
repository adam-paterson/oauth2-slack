<?php


namespace AdamPaterson\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

/**
 * Class SlackAuthorizedUser
 *
 * @package AdamPaterson\OAuth2\Client\Provider
 */
class SlackAuthorizedUser implements ResourceOwnerInterface
{
    /**
     * @var array
     */
    protected $response;

    /**
     * SlackAuthorizedUser constructor.
     *
     * @param $response
     */
    public function __construct(array $response)
    {
        $this->response = $response;
    }

    /**
     * Returns the identifier of the authorized resource owner.
     *
     * @return string
     */
    public function getId()
    {
        return $this->response['user_id'];
    }

    /**
     * Return all of the owner details available as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->response;
    }

    /**
     * Get authorized user url
     *
     * @return string|null
     */
    public function getUrl()
    {
        return $this->response['url'] ?: null;
    }

    /**
     * Get team
     *
     * @return string|null
     */
    public function getTeam()
    {
        return $this->response['team'] ?: null;
    }

    /**
     * Get user id
     *
     * @return string|null
     */
    public function getUser()
    {
        return $this->response['user'] ?: null;
    }

    /**
     * Get team id
     *
     * @return string|null
     */
    public function getTeamId()
    {
        return $this->response['team_id'] ?: null;
    }

    /**
     * Get user id
     *
     * @return string|null
     */
    public function getUserId()
    {
        return $this->response['user_id'] ?: null;
    }
}
