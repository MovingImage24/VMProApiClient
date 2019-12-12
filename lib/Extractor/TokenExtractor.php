<?php

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
     *
     * @param string $tokenString
     *
     * @return array
     */
    public function extract($tokenString)
    {
        return SimpleJWS::load($tokenString)->getPayload();
    }
}
