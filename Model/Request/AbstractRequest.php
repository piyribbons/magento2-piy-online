<?php
declare(strict_types=1);

namespace PiyRibbons\PiyOnline\Model\Request;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use PiyRibbons\PiyOnline\Model\PiyClientFactory;
use PiyRibbons\PiyOnline\Observer\Checkout\Cart\UpdateItemsRibbonText;
use Psr\Log\LoggerInterface;

class AbstractRequest
{

    public const ENDPOINT = '';
    public const METHOD = '';

    /**
     * @var PiyClientFactory
     */
    private PiyClientFactory $piyClientFactory;

    /**
     * @var Json
     */
    protected Json $json;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param PiyClientFactory $piyClientFactory
     * @param Json $json
     * @param LoggerInterface $logger
     */
    public function __construct(
        PiyClientFactory $piyClientFactory,
        Json $json,
        LoggerInterface $logger
    ) {
        $this->piyClientFactory = $piyClientFactory;
        $this->json = $json;
        $this->logger = $logger;
    }

    /**
     * Make a request to the PIY Dashboard
     *
     * @return bool|array
     */
    public function execute()
    {
        $piyClient = $this->piyClientFactory->create();

        try {
            $response = $piyClient->{static::METHOD}(
                static::ENDPOINT,
                $this->getRequestConfig()
            );

            [$result, $response] = $this->handleResponse($response);
        } catch (GuzzleException $e) {
            $this->logger->error(sprintf(
                'Unable to communicate with the PIY Dashboard API. Error: %s',
                $e->getMessage()
            ));
            $result = false;
            $response = [];
        }

        $this->afterExecute($result, $response);

        return $response;
    }

    /**
     * @return array
     */
    protected function getRequestConfig(): array
    {
        return [];
    }

    /**
     * @param $response
     * @return array
     */
    private function handleResponse($response): array
    {
        $result = true;

        if ($response->getStatusCode() !== 200) {
            $this->logger->error(sprintf(
                'Unable to communicate with the PIY Dashboard API. Error: %s',
                $response->getReasonPhrase()
            ));
            $result = false;
        }

        try {
            $response = $this->json->unserialize($response->getBody()->getContents());
        } catch (InvalidArgumentException $e) {
            $response = [];
        }

        return [$result, $response];
    }

    /**
     * @param bool $result
     * @param array $response
     * @return void
     */
    public function afterExecute(bool $result, array $response): void
    {
    }
}
