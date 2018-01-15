<?php

namespace MovingImage\Test\Manager;

use MovingImage\Client\VMPro\Entity\ApiCredentials;
use MovingImage\Client\VMPro\Entity\Token;
use MovingImage\Client\VMPro\Extractor\TokenExtractor;
use MovingImage\Client\VMPro\Manager\TokenManager;
use MovingImage\TestCase\ApiClientTestCase;
use Namshi\JOSE\SimpleJWS;
use Prophecy\Argument;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class TokenManagerTest extends ApiClientTestCase
{
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
     * @param array                       $videoManagers
     *
     * @return TokenManager
     */
    private function createTokenManager(
        SimpleJWS $token = null,
        CacheItemPoolInterface $cacheItemPool = null,
        array $videoManagers = [],
        $validForVideoManager = 1
    ) {
        $response = [];
        if ($token) {
            $response = [
                'accessToken' => $token->getTokenString(),
                'refreshToken' => $token->getTokenString(),
                'videoManagerList' => $videoManagers,
                'validForVideoManager' => $validForVideoManager,
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
