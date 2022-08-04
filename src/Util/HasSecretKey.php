<?php

namespace Ultainfinity\SolanaPhpSdk\Util;

interface HasSecretKey
{
    public function getSecretKey(): Buffer;
}
