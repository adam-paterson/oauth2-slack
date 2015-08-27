<?php

namespace AdamPaterson\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\GenericResourceOwner;

/**
 * Class SlackResourceOwner
 * @author Adam Paterson <hello@adampaterson.co.uk>
 *
 * @package AdamPaterson\OAuth2\Client\Provider
 */
class SlackResourceOwner extends GenericResourceOwner
{

    public function __construct(array $response, $resourceOwnerId)
    {
        parent::__construct($response, $resourceOwnerId);
    }

    public function getName()
    {
        return $this->response['name'] ?: null;
    }

    public function isDeleted()
    {
        return $this->response['deleted'] ?: null;
    }

    public function getStatus()
    {
        return $this->response['status'] ?: null;
    }

    public function getColor()
    {
        return $this->response['color'] ?: null;
    }

    public function getRealName()
    {
        return $this->response['real_name'] ?: null;
    }

    public function getTimeZone()
    {
        return $this->response['tz'] ?: null;
    }

    public function getTimeZoneLabel()
    {
        return $this->response['tz_label'] ?: null;
    }

    public function getTimeZoneOffset()
    {
        return $this->response['tz_offset'] ?: null;
    }

    public function getProfile()
    {
        return $this->response['profile'] ?: null;
    }

    public function isAdmin()
    {
        return $this->response['is_admin'] ?: null;
    }

    public function isOwner()
    {
        return $this->response['is_owner'] ?: null;
    }

    public function isPrimaryOwner()
    {
        return $this->response['is_primary_owner'] ?: null;
    }

    public function isRestricted()
    {
        return $this->response['is_restricted'] ?: null;
    }

    public function isUltraRestricted()
    {
        return $this->response['is_ultra_restricted'] ?: null;
    }
    
    public function isBot()
    {
        return $this->response['is_bot'] ?: null;
    }

    public function hasTwoFactorAuthentication()
    {
        return $this->response['has_2fa'] ?: null;
    }
}
