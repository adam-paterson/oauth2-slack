<?php

namespace AdamPaterson\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\GenericResourceOwner;

/**
 * Class SlackTeamResourceOwner
 * @author Adam Paterson <hello@adampaterson.co.uk>
 *
 * @package AdamPaterson\OAuth2\Client\Provider
 */
class SlackTeamResourceOwner extends GenericResourceOwner
{
    /**
     * @var array
     */
    protected $response;

    /**
     * SlackResourceOwner constructor.
     *
     * @param array $response
     */
    public function __construct(array $response = array())
    {
        $this->response = $response;
    }

    /**
     * Get team id.
     *
     * @return null
     */
    public function getId()
    {
        return $this->response['team']['id'] ?: null;
    }

    /**
     * Get team name.
     *
     * @return null
     */
    public function getName()
    {
        return $this->response['team']['name'] ?: null;
    }

    /**
     * Get team domain.
     *
     * @return null
     */
    public function getDomain()
    {
        return $this->response['team']['domain'] ?: null;
    }

    /**
     * @return null
     */
    public function getEmailDomain()
    {
        return $this->response['team']['email_domain'] ?: null;
    }

    /**
     * Get icons.
     *
     * @return null
     */
    public function getIcon()
    {
        return $this->response['team']['icon'] ?: null;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->response;
    }
}
