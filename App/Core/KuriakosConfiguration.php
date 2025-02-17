<?php

namespace App\Core;

use Dotenv\Dotenv;

class KuriakosConfiguration {

    private $configuration;

    public function __construct(string $file)
    {
        $this->configuration = Dotenv::createImmutable('../../'. __DIR__);
        $this->configuration->load();
    }

    public static function get($key)
    {
        return getenv($key);
    }

}