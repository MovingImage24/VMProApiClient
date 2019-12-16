<?php

declare(strict_types=1);

namespace MovingImage\Client\VMPro\Extractor;

use Namshi\JOSE\SimpleJWS;

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
        return SimpleJWS::load($tokenString)->getPayload();
    }
}
