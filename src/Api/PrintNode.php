<?php

namespace Consignr\FilamentPrintNode\Api;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Consignr\FilamentPrintNode\Api\BasicAuthenticator;

class PrintNode extends Connector
{
    use AcceptsJson;

    public function __construct(
        public readonly string $username,
        public readonly ?string $password = null
    ){}
    
    protected function defaultAuth(): BasicAuthenticator
    {
        return new BasicAuthenticator($this->username, $this->password);
    }

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return 'https://api.printnode.com';
    }

    /**
     * Default headers for every request
     */
    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Default HTTP client options
     */
    protected function defaultConfig(): array
    {
        return [];
    }
}
