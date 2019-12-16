<?php

namespace MovingImage\Test\Manager;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use MovingImage\Client\VMPro\Entity\ApiCredentials;
use MovingImage\Client\VMPro\Entity\Token;
use MovingImage\Client\VMPro\Extractor\TokenExtractor;
use MovingImage\Client\VMPro\Manager\TokenManager;
use MovingImage\TestCase\ApiClientTestCase;
use MovingImage\VMPro\TestUtil\GuzzleResponseGenerator;
use MovingImage\VMPro\TestUtil\PrivateMethodCaller;
use Namshi\JOSE\SimpleJWS;
use Prophecy\Argument;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class TokenManagerTest extends ApiClientTestCase
{
    use PrivateMethodCaller;
    use GuzzleResponseGenerator;

    /**
     * Tests the response of the createNewTokens method.
     *
     * @covers \TokenManager::createNewTokens()
     */
    public function testCreateNewTokensResponse()
    {
        $tokenManager = $this->createTokenManager($this->createSimpleJwsToken());
        $tokens = $this->callMethod($tokenManager, 'createNewTokens', []);
        $this->assertArrayHasKey('accessToken', $tokens);
        $this->assertArrayHasKey('refreshToken', $tokens);
        /** @var Token $accessToken */
        $accessToken = $tokens['accessToken'];
        /** @var Token $refreshToken */
        $refreshToken = $tokens['refreshToken'];
        $this->assertInstanceOf(Token::class, $accessToken);
        $this->assertInstanceOf(Token::class, $refreshToken);
        $this->assertNotEmpty($accessToken->getTokenString());
        $this->assertNotEmpty($refreshToken->getTokenString());
    }

    /**
     * Tests that the correct Guzzle 6 request is sent from the createNewTokens method.
     *
     * @covers \TokenManager::createNewTokens()
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
            ->method('__call')
            ->willReturnCallback(function ($method, $params) use ($phpUnit, $clientResponse) {
                $phpUnit->assertSame('post', $method);
                $requestUrl = $params[0];
                $requestOptions = $params[1];
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
     *
     * @covers \TokenManager::createAccessTokenFromRefreshToken()
     */
    public function testCreateAccessTokenFromRefreshTokenResponse()
    {
        $jwsToken = $this->createSimpleJwsToken();
        $tokenManager = $this->createTokenManager($jwsToken);
        $tokenExtractor = new TokenExtractor();
        $refreshToken = new Token($jwsToken->getTokenString(), $tokenExtractor->extract($jwsToken->getTokenString()));

        /** @var Token $accessToken */
        $accessToken = $this->callMethod($tokenManager, 'createAccessTokenFromRefreshToken', [$refreshToken]);
        $this->assertInstanceOf(Token::class, $accessToken);
        $this->assertNotEmpty($accessToken->getTokenString());
    }

    /**
     * Tests that the correct Guzzle 6 request is sent from the createAccessTokenFromRefreshToken method.
     *
     * @covers \TokenManager::createAccessTokenFromRefreshToken()
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
            ->method('__call')
            ->willReturnCallback(function ($method, $params) use ($phpUnit, $clientResponse, $refreshToken) {
                $phpUnit->assertSame('post', $method);
                $requestUrl = $params[0];
                $requestOptions = $params[1];
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
     * Test the scenario when getToken is called and there is no token in cache.
     *
     * @covers \TokenManager::getToken()
     */
    public function testGetTokenWithoutCachedData()
    {
        $tokenExpirationTimestamp = time() + 300;
        //cached token is supposed to expire 30 seconds before the token itself expires
        $expectedCacheExpirationDate = (new \DateTime())->setTimestamp($tokenExpirationTimestamp - 30);

        $cacheItem = $this->prophesize(CacheItemInterface::class);
        $cachePool = $this->createMock(CacheItemPoolInterface::class);
        $cachePool
            ->method('getItem')
            ->willReturn($cacheItem->reveal())
        ;

        $cacheItem
            ->isHit()
            ->shouldBeCalled()
            ->willReturn(false)
        ;

        $cacheItem
            ->set(Argument::type(Token::class))
            ->shouldBeCalled()
        ;

        $cacheItem
            ->expiresAt($expectedCacheExpirationDate)
            ->shouldBeCalled()
        ;

        $tokenManager = $this->createTokenManager($this->createSimpleJwsToken($tokenExpirationTimestamp), $cachePool);
        $tokenManager->getToken();
    }

    /**
     * Test the scenario when getToken is called and there is a token in cache.
     *
     * @covers \TokenManager::getToken()
     */
    public function testGetTokenWithCachedData()
    {
        $simpleJwsToken = $this->createSimpleJwsToken();

        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cachePool = $this->createMock(CacheItemPoolInterface::class);
        $cachePool
            ->method('getItem')
            ->willReturn($cacheItem)
        ;

        $cacheItem
            ->method('isHit')
            ->willReturn(true)
        ;

        $cacheItem
            ->method('get')
            ->willReturn(new Token($simpleJwsToken->getTokenString(), $simpleJwsToken->getPayload(), 1))
        ;

        $tokenManager = $this->createTokenManager(null, $cachePool);
        $returnedToken = $tokenManager->getToken();

        $this->assertSame($simpleJwsToken->getTokenString(), $returnedToken);
    }

    /**
     * Creates an instance of TokenManager, configured to return the provided token.
     *
     * @param SimpleJWS|null              $token
     * @param CacheItemPoolInterface|null $cacheItemPool
     *
     * @return TokenManager
     */
    private function createTokenManager(
        SimpleJWS $token = null,
        CacheItemPoolInterface $cacheItemPool = null
    ) {
        $response = [];
        if ($token) {
            $response = [
                'access_token' => $token->getTokenString(),
                'refresh_token' => $token->getTokenString(),
            ];
        }

        $httpClient = $this->createMockGuzzleClient(200, [], $response);

        $credentials = new ApiCredentials('user', 'pass');

        return new TokenManager($httpClient, $credentials, new TokenExtractor(), $cacheItemPool);
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
