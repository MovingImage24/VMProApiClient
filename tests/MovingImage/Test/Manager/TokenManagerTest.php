<?php

namespace MovingImage\Test\Manager;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use MovingImage\Client\VMPro\Entity\ApiCredentials;
use MovingImage\Client\VMPro\Entity\Token;
use MovingImage\Client\VMPro\Extractor\TokenExtractor;
use MovingImage\Client\VMPro\Manager\TokenManager;
use MovingImage\TestCase\ApiClientTestCase;
use MovingImage\VMPro\TestUtil\GuzzleResponseGenerator;
use MovingImage\VMPro\TestUtil\PrivateMethodCaller;
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
        $tokenString = $this->createJwtTokenString();
        $tokenManager = $this->createTokenManager($tokenString);
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
        $tokenString = $this->createJwtTokenString();
        $oauthResponse = json_encode([
            'access_token' => $tokenString,
            'refresh_token' => $tokenString,
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
        $tokenString = $this->createJwtTokenString();
        $tokenManager = $this->createTokenManager($tokenString);
        $tokenExtractor = new TokenExtractor();
        $refreshToken = new Token($tokenString, $tokenExtractor->extract($tokenString));

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
        $tokenString = $this->createJwtTokenString();
        $oauthResponse = json_encode([
            'access_token' => $tokenString,
            'refresh_token' => $tokenString,
        ]);

        $tokenExtractor = new TokenExtractor();
        $refreshToken = new Token($tokenString, $tokenExtractor->extract($tokenString));

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
    private function createTokenManager(?string $tokenString = null): TokenManager
    {
        $response = [];
        if ($tokenString) {
            $response = [
                'access_token' => $tokenString,
                'refresh_token' => $tokenString,
            ];
        }

        $httpClient = $this->createMockGuzzleClient(200, [], $response);

        $credentials = new ApiCredentials('user', 'pass');

        return new TokenManager($httpClient, $credentials, new TokenExtractor());
    }

    /**
     * Creates a JWT token string with only 'exp' property set.
     */
    private function createJwtTokenString(?int $expirationTimestamp = null): string
    {
        if (!$expirationTimestamp) {
            $expirationTimestamp = time() + 300;
        }

        $header = JWT::urlsafeB64Encode(JWT::jsonEncode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload = JWT::urlsafeB64Encode(JWT::jsonEncode(['exp' => $expirationTimestamp]));
        $signature = JWT::urlsafeB64Encode('test-signature');

        return "$header.$payload.$signature";
    }
}
