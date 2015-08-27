<?php
namespace AdamPaterson\OAuth2\Client\Test\Provider;

use AdamPaterson\OAuth2\Client\Provider\Slack;
use Mockery as m;
use ReflectionClass;

class SlackTest extends \PHPUnit_Framework_TestCase
{
    protected $provider;

    protected static function getMethod($name)
    {
        $class = new ReflectionClass('AdamPaterson\OAuth2\Client\Provider\Slack');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    protected function setUp()
    {
        $this->provider = new Slack([
            'clientId'      => 'mock_client_id',
            'clientSecret'  => 'mock_secret',
            'redirectUri'   => 'none',
        ]);
    }

    public function tearDown()
    {
        m::close();
        parent::tearDown();
    }

    public function testAuthorizationUrl()
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertArrayHasKey('state', $query);
        $this->assertArrayHasKey('scope', $query);
        $this->assertArrayHasKey('response_type', $query);
        $this->assertArrayHasKey('approval_prompt', $query);
        $this->assertNotNull($this->provider->getState());
    }

    public function testGetAuthoriztionUrl()
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);

        $this->assertEquals('/oauth/authorize', $uri['path']);
    }

    public function testGetBaseAccessTokenUrl()
    {
        $params = [];
        $url = $this->provider->getBaseAccessTokenUrl($params);
        $uri = parse_url($url);
        $this->assertEquals('/api/oauth.access', $uri['path']);
    }

    public function testGetAccessToken()
    {
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getBody')->andReturn('{"access_token": "mock_access_token", "expires_in": 3600}');
        $response->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->times(1)->andReturn($response);
        $this->provider->setHttpClient($client);
        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
        $this->assertEquals('mock_access_token', $token->getToken());
        $this->assertLessThanOrEqual(time() + 3600, $token->getExpires());
        $this->assertGreaterThanOrEqual(time(), $token->getExpires());
        $this->assertNull($token->getRefreshToken());
        $this->assertNull($token->getResourceOwnerId());
    }

    public function testCheckResponseThrowsIdentityProviderException()
    {
        $method = self::getMethod('checkResponse');
        $responseInterface = m::mock('Psr\Http\Message\ResponseInterface');
        $data = ['ok' => false];
        try {
            $method->invoke($this->provider, $responseInterface, $data);
        } catch (\Exception $e) {
            $this->assertEquals(400, $e->getCode());
            $this->assertEquals("Unknown error", $e->getMessage());
        }
    }

    public function testGetTeamInfo()
    {
        $id = rand(1000,999);
        $name = uniqid();
        $domain = uniqid();
        $emailDomain = uniqid();
        $icon = [
            0 => uniqid(),
            1 => uniqid(),
        ];

        $postResponse = m::mock('Psr\Http\Message\ResponseInterface');
        $postResponse->shouldReceive('getBody')->andReturn('{"access_token": "mock_access_token"}');
        $postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);

        $teamResponse = m::mock('Psr\Http\Message\ResponseInterface');
        $teamResponse->shouldReceive('getBody')->andReturn('{"ok":true,"team":{"id":"'.$id.'","name":"'.$name.'","domain":"'.$domain.'","email_domain":"'.$emailDomain.'","icon":{"0":"'.$icon[0].'","1":"'.$icon[1].'"}}}');
        $teamResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);

        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')
            ->times(2)
            ->andReturn($postResponse, $teamResponse);

        $this->provider->setHttpClient($client);
        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
        $user = $this->provider->getResourceOwner($token);
        $this->assertEquals($id, $user->getId());
        $this->assertEquals($id, $user->toArray()['team']['id']);
        $this->assertEquals($name, $user->getName());
        $this->assertEquals($name, $user->toArray()['team']['name']);
        $this->assertEquals($domain, $user->getDomain());
        $this->assertEquals($domain, $user->toArray()['team']['domain']);
        $this->assertEquals($emailDomain, $user->getEmailDomain());
        $this->assertEquals($emailDomain, $user->toArray()['team']['email_domain']);
        $this->assertEquals($icon, $user->getIcon());
        $this->assertEquals($icon, $user->toArray()['team']['icon']);
    }
}
