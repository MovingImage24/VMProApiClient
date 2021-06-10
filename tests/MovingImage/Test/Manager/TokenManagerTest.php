<?php

namespace MovingImage\Test\Manager;

use GuzzleHttp\Client;
use MovingImage\Client\VMPro\Entity\ApiCredentials;
use MovingImage\Client\VMPro\Entity\Token;
use MovingImage\Client\VMPro\Extractor\TokenExtractor;
use MovingImage\Client\VMPro\Manager\TokenManager;
use MovingImage\TestCase\ApiClientTestCase;
use MovingImage\VMPro\TestUtil\GuzzleResponseGenerator;
use MovingImage\VMPro\TestUtil\PrivateMethodCaller;
use Namshi\JOSE\SimpleJWS;
use Prophecy\PhpUnit\ProphecyTrait;

class TokenManagerTest extends ApiClientTestCase
{
    use PrivateMethodCaller;
    use GuzzleResponseGenerator;
    use ProphecyTrait;

    /**
     * Tests the response of the createNewTokens method.
     */
    public function testCreateNewTokensResponse()
    {
        $tokenManager = $this->createTokenManager($this->createSimpleJwsToken());
        $tokens = $this->callMethod($tokenManager, 'createNewTokens', []);
        self::assertArrayHasKey('accessToken', $tokens);
        self::assertArrayHasKey('refreshToken', $tokens);
        /** @var Token $accessToken */
        $accessToken = $tokens['accessToken'];
        /** @var Token $refreshToken */
        $refreshToken = $tokens['refreshToken'];
        self::assertInstanceOf(Token::class, $accessToken);
        self::assertInstanceOf(Token::class, $refreshToken);
        self::assertNotEmpty($accessToken->getTokenString());
        self::assertNotEmpty($refreshToken->getTokenString());
    }

    /**
     * Tests that the correct Guzzle 6 request is sent from the createNewTokens method.
     */
    public function testCreateNewTokensGuzzle6Request()
    {
        $token = $this->createSimpleJwsToken();
        $oauthResponse = json_encode([
            'access_token' => $token->getTokenString(),
            'refresh_token' => $token->getTokenString(),
        ]);

        $httpClient = $this->createMock(Client::class);
        $clientResponse = $this->generateGuzzleResponse(200, [], $oauthResponse);

        $phpUnit = $this;

        $httpClient
            ->method('request')
            ->willReturnCallback(function ($method, $uri, $params) use ($phpUnit, $clientResponse) {
                $phpUnit->assertSame('post', strtolower($method));
                $requestUrl = $uri;
                $requestOptions = $params;
                $phpUnit->assertArrayHasKey('form_params', $requestOptions);
                $body = $requestOptions['form_params'];
                $phpUnit->assertSame('', $requestUrl);
                $phpUnit->assertSame('anonymous', $body['client_id']);
                $phpUnit->assertSame('password', $body['grant_type']);
                $phpUnit->assertSame('token', $body['response_type']);
                $phpUnit->assertSame('openid', $body['scope']);
                $phpUnit->assertSame('user', $body['username']);
                $phpUnit->assertSame('pass', $body['password']);

                return $clientResponse;
            })
        ;

        $credentials = new ApiCredentials('user', 'pass');
        $tokenManager = new TokenManager($httpClient, $credentials, new TokenExtractor());
        $this->callMethod($tokenManager, 'createNewTokens', []);
    }

    /**
     * Tests the response of the createAccessTokenFromRefreshToken method.
     */
    public function testCreateAccessTokenFromRefreshTokenResponse()
    {
        $jwsToken = $this->createSimpleJwsToken();
        $tokenManager = $this->createTokenManager($jwsToken);
        $tokenExtractor = new TokenExtractor();
        $refreshToken = new Token($jwsToken->getTokenString(), $tokenExtractor->extract($jwsToken->getTokenString()));

        /** @var Token $accessToken */
        $accessToken = $this->callMethod($tokenManager, 'createAccessTokenFromRefreshToken', [$refreshToken]);
        self::assertInstanceOf(Token::class, $accessToken);
        self::assertNotEmpty($accessToken->getTokenString());
    }

    /**
     * Tests that the correct Guzzle 6 request is sent from the createAccessTokenFromRefreshToken method.
     */
    public function testCreateAccessTokenFromRefreshTokenGuzzle6Request()
    {
        $token = $this->createSimpleJwsToken();
        $oauthResponse = json_encode([
            'access_token' => $token->getTokenString(),
            'refresh_token' => $token->getTokenString(),
        ]);

        $jwsToken = $this->createSimpleJwsToken();
        $tokenExtractor = new TokenExtractor();
        $refreshToken = new Token($jwsToken->getTokenString(), $tokenExtractor->extract($jwsToken->getTokenString()));

        $httpClient = $this->createMock(Client::class);
        $clientResponse = $this->generateGuzzleResponse(200, [], $oauthResponse);

        $phpUnit = $this;

        $httpClient
            ->method('request')
            ->willReturnCallback(function ($method, $uri, $params) use ($phpUnit, $clientResponse, $refreshToken) {
                $phpUnit->assertSame('post', strtolower($method));
                $requestUrl = $uri;
                $requestOptions = $params;
                $phpUnit->assertArrayHasKey('form_params', $requestOptions);
                $body = $requestOptions['form_params'];
                $phpUnit->assertSame('', $requestUrl);
                $phpUnit->assertSame('anonymous', $body['client_id']);
                $phpUnit->assertSame('refresh_token', $body['grant_type']);
                $phpUnit->assertSame($refreshToken->getTokenString(), $body['refresh_token']);

                return $clientResponse;
            })
        ;

        $credentials = new ApiCredentials('user', 'pass');
        $tokenManager = new TokenManager($httpClient, $credentials, new TokenExtractor());
        $this->callMethod($tokenManager, 'createAccessTokenFromRefreshToken', [$refreshToken]);
    }

    /**
     * Creates an instance of TokenManager, configured to return the provided token.
     */
    private function createTokenManager(?SimpleJWS $token = null): TokenManager
    {
        $response = [];
        if ($token) {
            $response = [
                'access_token' => $token->getTokenString(),
                'refresh_token' => $token->getTokenString(),
            ];
        }

        $httpClient = $this->createMockGuzzleClient(200, [], $response);

        $credentials = new ApiCredentials('user', 'pass');

        return new TokenManager($httpClient, $credentials, new TokenExtractor());
    }

    /**
     * Creates a SimpleJWS token, with only 'exp' property set.
     *
     * @param null $expirationTimestamp
     *
     * @return SimpleJWS
     */
    private function createSimpleJwsToken($expirationTimestamp = null)
    {
        if (!$expirationTimestamp) {
            $expirationTimestamp = time() + 300;
        }

        $token = new SimpleJWS([
            'alg' => 'HS256',
            'typ' => 'JWT',
        ]);

        $token->setPayload([
            'exp' => $expirationTimestamp,
        ]);

        return $token;
    }
}
