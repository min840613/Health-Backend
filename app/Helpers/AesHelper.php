<?php

namespace App\Helpers;

/**
 * Class AesHelper
 * @package App\Helpers
 */
class AesHelper
{
    /** @var string */
    private $aes_method;

    /** @var string */
    private $key;

    /** @var string */
    private $decrypt_option;

    /** @var string */
    private $iv;

    /**
     * AesHelper constructor.
     */
    public function __construct()
    {
        $this->aes_method = config('aes.method');
        $this->key = config('aes.key');
        $this->decrypt_option = config('aes.options.decrypt');
        $this->iv = config('aes.iv');
    }

    /**
     * @param string $encrypted
     * @return false|string
     */
    public function decrypt(string $encrypted): string
    {
        return openssl_decrypt(hex2bin($encrypted), $this->aes_method, $this->key, $this->decrypt_option, $this->iv);
    }
}
