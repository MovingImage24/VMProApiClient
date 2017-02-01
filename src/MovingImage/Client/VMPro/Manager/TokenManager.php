<?php

namespace MovingImage\Client\VMPro\Manager;

use GuzzleHttp\ClientInterface;
use MovingImage\Client\VMPro\Entity\ApiCredentials;
use MovingImage\Client\VMPro\Entity\Token;
use MovingImage\Client\VMPro\Extractor\TokenExtractor;
use MovingImage\Util\Logging\Traits\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;

/**
 * Class TokenManager.
 *
 * @author Ruben Knol <ruben.knol@movingimage.com>
 */
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
     * TokenManager constructor.
     *
     * @param ClientInterface $httpClient
     * @param ApiCredentials  $credentials
     */
    public function __construct(
        ClientInterface $httpClient,
        ApiCredentials $credentials,
        TokenExtractor $tokenExtractor
    ) {
        $this->httpClient = $httpClient;
        $this->credentials = $credentials;
        $this->tokenExtractor = $tokenExtractor;
    }

    /**
     * Create completely fresh Access + Refresh tokens.
     *
     * @TODO Implement proper error handling
     *
     * @return array
     */
    protected function createNewTokens()
    {
        $logger = $this->getLogger();
        $logger->debug('Starting request to create fresh access & refresh tokens');

        $response = $this->httpClient->post('auth/login', [
            'json' => [
                'username' => $this->credentials->getUsername(),
                'password' => $this->credentials->getPassword(),
            ],
            'headers' => [
                'accept: application/json',
                'cache-control: no-cache',
                'content-type: application/json',
            ],
        ]);

        $data = \json_decode($response->getBody(), true);
        $logger->debug('Successfully retrieved new access & refresh tokens', $data);

        return [
            'accessToken' => new Token(
                $data['accessToken'],
                $this->tokenExtractor->extract($data['accessToken']),
                $data['validForVideoManager']
            ),
            'refreshToken' => new Token(
                $data['refreshToken'],
                $this->tokenExtractor->extract($data['refreshToken']),
                null
            ),
        ];
    }

    /**
     * Create a new access token for a video manager using a refresh token.
     *
     * @param Token $refreshToken
     * @param int   $videoManagerId
     *
     * @return Token
     */
    protected function createAccessTokenFromRefreshToken(Token $refreshToken, $videoManagerId)
    {
        $logger = $this->getLogger();
        $logger->debug('Starting request to create fresh access token from refresh token');

        $response = $this->httpClient->post(sprintf('auth/refresh/%d', $videoManagerId), [
            'json' => [
                'refreshToken' => $refreshToken->getTokenString(),
            ],
            'headers' => [
                'accept: application/json',
                'cache-control: no-cache',
                'content-type: application/json',
            ],
        ]);

        $data = \json_decode($response->getBody(), true);
        $logger->debug('Successfully retrieved new access token', $data);

        return new Token(
            $data['accessToken'],
            $this->tokenExtractor->extract($data['accessToken']),
            $videoManagerId
        );
    }

    /**
     * Log information about which tokens we have.
     */
    protected function logTokenData()
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
     *
     * @param int $videoManagerId Which video manager a token is requested for
     * @TODO Refactor token storage to support multiple re-usable tokens, one per video manager
     *
     * @return string
     */
    public function getToken($videoManagerId = null)
    {
        $logger = $this->getLogger();
        $this->logTokenData();

        // Access token has expired, but expiration token has not expired.
        // Issue ourselves a new access token for the same video manager.
        if (!is_null($this->accessToken)
            && $this->accessToken->expired()
            && !$this->refreshToken->expired()) {
            $logger->info('Access token has expired - getting new one for same video manager with refresh token');
            $tokenData = $this->createAccessTokenFromRefreshToken(
                $this->refreshToken,
                $this->accessToken->getVideoManagerId()
            );

            $this->accessToken = $tokenData['accessToken'];
        } elseif (is_null($this->accessToken)
            || (!is_null($this->refreshToken) && $this->refreshToken->expired())) {
            // Either we have no token, or the refresh token has expired
            // so we will need to generate completely new tokens
            $logger->info('No access token, or refresh token has expired - generate completely new ones');
            $tokenData = $this->createNewTokens();

            $this->accessToken = $tokenData['accessToken'];
            $this->refreshToken = $tokenData['refreshToken'];
        }

        // Video manager is not matching with the one that our token
        // was generated with - issue ourselves a token for the video manager
        // we need.
        if (!is_null($videoManagerId)
            && isset($this->accessToken)
            && $this->accessToken->getVideoManagerId() != $videoManagerId) {
            $logger->info('Attempting to use token for different video manager - generate valid access token');
            $this->accessToken = $this->createAccessTokenFromRefreshToken($this->refreshToken, $videoManagerId);
        }

        return $this->accessToken->getTokenString();
    }
}
