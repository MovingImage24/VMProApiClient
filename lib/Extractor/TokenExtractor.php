<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Extractor;

use Firebase\JWT\JWT;

/**
 * @codeCoverageIgnore - Ignore this as it's just external dependency wrapper
 */
class TokenExtractor
{
    /**
     * Wrapper method to be able to more easily mock extracting information from
     * JWT token strings from the TokenManager.
     */
    public function extract(string $tokenString): array
    {
        // Decode without verification - we only need to read the payload claims
        $parts = explode('.', $tokenString);
        if (count($parts) !== 3) {
            throw new \InvalidArgumentException('Invalid JWT token format');
        }

        $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($parts[1]));

        return (array) $payload;
    }
}
