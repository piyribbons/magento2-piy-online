<?php
declare(strict_types=1);

namespace PiyRibbons\PiyOnline\Model;

use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;

class PiyClientFactory
{
    /**
     * @var ClientFactory
     */
    private ClientFactory $clientFactory;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param ClientFactory $clientFactory
     * @param Config $config
     */
    public function __construct(
        ClientFactory $clientFactory,
        Config $config

    ) {
        $this->clientFactory = $clientFactory;
        $this->config = $config;
    }

    /**
     * @return Client
     */
    public function create(): Client
    {
        $config = [
            'base_uri' => $this->config->getApiBaseUrl(),
            'headers' => [
                'Cache-Control' => 'nocache',
                'Authorization' => $this->getAuthorizationHeader()
            ],
            'verify' => false
        ];

        return $this->clientFactory->create(['config' => $config]);
    }

    /**
     * @return string
     */
    private function getAuthorizationHeader(): string
    {
        $authHeader = [];

        $basicAuthUser = $this->config->getHtaccessUser();
        $basicAuthPassword = $this->config->getHtaccessPassword();

        if ($basicAuthUser && $basicAuthPassword) {
            $authHeader[] = 'Basic ' . base64_encode("{$basicAuthUser}:{$basicAuthPassword}");
        }

        $authHeader[] = "Bearer {$this->config->getApiKey()}";

        return implode(', ', $authHeader);
    }
}
