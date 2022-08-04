<?php

namespace Ultainfinity\SolanaPhpSdk\Util;

use Ultainfinity\SolanaPhpSdk\PublicKey;

interface HasPublicKey
{
    public function getPublicKey(): PublicKey;
}
