# Slack Provider for OAuth 2.0 Client
[![Latest Version](https://img.shields.io/github/release/adam-paterson/oauth2-slack.svg?style=flat-square)](https://github.com/adam-paterson/oauth2-slack/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/adam-paterson/oauth2-slack/master.svg?style=flat-square)](https://travis-ci.org/adam-paterson/oauth2-slack)
[![HHVM Status](https://img.shields.io/hhvm/adam-paterson/oauth2-slack.svg?style=flat-square)](http://hhvm.h4cc.de/package/adam-paterson/oauth2-slack)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/adam-paterson/oauth2-slack.svg?style=flat-square)](https://scrutinizer-ci.com/g/adam-paterson/oauth2-slack/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/adam-paterson/oauth2-slack.svg?style=flat-square)](https://scrutinizer-ci.com/g/adam-paterson/oauth2-slack)
[![Dependency Status](https://img.shields.io/versioneye/d/php/adam-paterson:oauth2-slack/1.1.2.svg?style=flat-square)](https://www.versioneye.com/php/adam-paterson:oauth2-slack/1.1.2)
[![Total Downloads](https://img.shields.io/packagist/dt/adam-paterson/oauth2-slack.svg?style=flat-square)](https://packagist.org/packages/adam-paterson/oauth2-slack)

This package provides Slack OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

To install, use composer:

```
composer require adam-paterson/oauth2-slack
```
## Usage

Usage is the same as The League's OAuth client, using `\AdamPaterson\OAuth2\Client\Provider\Slack` as the provider.

### Authorization Code Flow

session_start();

```php
<?php
$provider = new \AdamPaterson\OAuth2\Client\Provider\Slack([
    'clientId'          => '{slack-client-id}',
    'clientSecret'      => '{slack-client-secret}',
    'redirectUri'       => 'https://example.com/callback-url',
]);

if (!isset($_GET['code'])) {

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: '.$authUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    $slackWebhookInfo = $token->getSlackWebhookInfo();
    /*
        {
        "scope": "incoming-webhook,commands"
        "team_name": "Team Installing Your Hook",
        "incoming_webhook": {
            "url": "https://hooks.slack.com/TXXXXX/BXXXXX/XXXXXXXXXX",
            "channel": "#channel-it-will-post-to",
            "configuration_url": "https://teamname.slack.com/services/BXXXXX"
        }
    }

    */


    // Optional: Now you have a token you can look up a users profile data
    try {

        // We got an access token, let's now get the user's details
        $team = $provider->getResourceOwner($token);

        // Use these details to create a new profile
        printf('Hello %s!', $team->getName());

    } catch (Exception $e) {

        // Failed to get user details
        exit('Oh dear...');
    }

    // Use this to interact with an API on the users behalf
    echo $token->getToken();
}

```

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/adam-paterson/oauth2-slack/blob/master/CONTRIBUTING.md) for details.


## Credits

- [Adam Paterson](https://github.com/adam-paterson)
- [All Contributors](https://github.com/adam-paterson/oauth2-slack/contributors)


## License

The MIT License (MIT). Please see [License File](https://github.com/adam-paterson/oauth2-slack/blob/master/LICENSE) for more information.
