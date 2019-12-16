<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Manager;

use Cache\Adapter\Void\VoidCachePool;
use GuzzleHttp\ClientInterface;
use MovingImage\Client\VMPro\Entity\ApiCredentials;
use MovingImage\Client\VMPro\Entity\Token;
use MovingImage\Client\VMPro\Extractor\TokenExtractor;
use MovingImage\Client\VMPro\Util\Logging\Traits\LoggerAwareTrait;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerAwareInterface;

class TokenManager implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var ApiCredentials
     */
    protected $credentials;

    /**
     * @var TokenExtractor
     */
    private $tokenExtractor;

    /**
     * @var Token
     */
    private $accessToken;

    /**
     * @var Token
     */
    private $refreshToken;

    /**
     * If provided in the constructor, will be used to cache the access token.
     *
     * @var CacheItemPoolInterface
     */
    private $cacheItemPool;

    public function __construct(
        ClientInterface $httpClient,
        ApiCredentials $credentials,
        TokenExtractor $tokenExtractor,
        ?CacheItemPoolInterface $cacheItemPool = null
    ) {
        $this->httpClient = $httpClient;
        $this->credentials = $credentials;
        $this->tokenExtractor = $tokenExtractor;
        $this->cacheItemPool = $cacheItemPool ?: new VoidCachePool();
    }

    /**
     * Create completely fresh Access + Refresh tokens.
     *
     * @TODO Implement proper error handling
     */
    protected function createNewTokens(): array
    {
        $logger = $this->getLogger();
        $logger->debug('Starting request to create fresh access & refresh tokens');

        $body = [
            'client_id' => 'anonymous',
            'grant_type' => 'password',
            'response_type' => 'token',
            'scope' => 'openid',
            'username' => $this->credentials->getUsername(),
            'password' => $this->credentials->getPassword(),
        ];

        $response = $this->sendPostRequest($body);

        $logger->debug('Successfully retrieved new access & refresh tokens', $response);

        return [
            'accessToken' => new Token(
                $response['access_token'],
                $this->tokenExtractor->extract($response['access_token'])
            ),
            'refreshToken' => new Token(
                $response['refresh_token'],
                $this->tokenExtractor->extract($response['refresh_token'])
            ),
        ];
    }

    /**
     * Create a new access token for a video manager using a refresh token.
     */
    protected function createAccessTokenFromRefreshToken(Token $refreshToken): Token
    {
        $logger = $this->getLogger();
        $logger->debug('Starting request to create fresh access token from refresh token');

        $body = [
            'client_id' => 'anonymous',
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken->getTokenString(),
        ];

        $response = $this->sendPostRequest($body);

        $logger->debug('Successfully retrieved new access token', $response);

        return new Token(
            $response['access_token'],
            $this->tokenExtractor->extract($response['access_token'])
        );
    }

    /**
     * Log information about which tokens we have.
     */
    protected function logTokenData(): void
    {
        $this->getLogger()->debug('Token information', [
            'accessTokenExists' => isset($this->accessToken),
            'accessTokenExpiration' => isset($this->accessToken) ? $this->accessToken->getTokenData()['exp'] : null,
            'accessTokenHasExpired' => isset($this->accessToken) ? $this->accessToken->expired() : null,
            'refreshTokenExists' => isset($this->refreshToken),
            'refreshTokenExpiration' => isset($this->refreshToken) ? $this->refreshToken->getTokenData()['exp'] : null,
            'refreshTokenHasExpired' => isset($this->refreshToken) ? $this->refreshToken->expired() : null,
            'localTime' => time(),
        ]);
    }

    /**
     * Retrieve a valid token.
     */
    public function getToken(): string
    {
        $logger = $this->getLogger();
        $this->logTokenData();

        $cacheKey = sha1(sprintf('%s.%s', __METHOD__, json_encode(func_get_args())));
        $cacheItem = $this->cacheItemPool->getItem($cacheKey);
        if (!$this->accessToken && $cacheItem->isHit()) {
            $this->accessToken = $cacheItem->get();
        }

        // Access token has expired, but expiration token has not expired.
        // Issue ourselves a new access token for the same video manager.
        if (!is_null($this->accessToken)
            && $this->accessToken->expired()
            && !is_null($this->refreshToken)
            && !$this->refreshToken->expired()) {
            $logger->info('Access token has expired - getting new one for same video manager with refresh token');
            $this->accessToken = $this->createAccessTokenFromRefreshToken($this->refreshToken);
        } elseif (is_null($this->accessToken)
            || (!is_null($this->refreshToken) && $this->refreshToken->expired())) {
            // Either we have no token, or the refresh token has expired
            // so we will need to generate completely new tokens
            $logger->info('No access token, or refresh token has expired - generate completely new ones');
            $tokenData = $this->createNewTokens();

            $this->accessToken = $tokenData['accessToken'];
            $this->refreshToken = $tokenData['refreshToken'];
        }

        $cacheItem->set($this->accessToken);
        $cacheItem->expiresAt((new \DateTime())
            ->setTimestamp($this->accessToken->getTokenData()['exp'])
            ->sub(new \DateInterval('PT30S'))
        );
        $this->cacheItemPool->save($cacheItem);

        return $this->accessToken->getTokenString();
    }

    /**
     * Sends a post request to the OAuth endpoint.
     *
     * @return mixed
     */
    private function sendPostRequest(array $body): array
    {
        $response = $this->httpClient->post('', [
            'form_params' => $body,
        ]);

        return \json_decode($response->getBody()->getContents(), true);
    }
}
