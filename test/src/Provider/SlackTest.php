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

    public function testGetResourceOwnerDetailsUrl()
    {
        $token = m::mock('League\OAuth2\Client\Token\AccessToken', [['access_token' => 'mock_access_token']]);
        $url = $this->provider->getResourceOwnerDetailsUrl($token);
        $uri = parse_url($url);

    }

    public function testGetAuthorizationUrl()
    {
        $params = [];
        $url = $this->provider->getAuthorizationUrl($params);
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

    public function testGetAuthorizedUserTestUrl()
    {
        $method = self::getMethod('getAuthorizedUserTestUrl');
        $token = m::mock('League\OAuth2\Client\Token\AccessToken', [['access_token' => 'mock_access_token']]);

        $url = $method->invoke($this->provider, $token);
        $uri = parse_url($url);

        $this->assertEquals('/api/auth.test', $uri['path']);

    }

    public function testGetAuthorizedUser()
    {
        $token = m::mock('League\OAuth2\Client\Token\AccessToken', [['access_token' => 'mock_access_token']]);

    }
}
