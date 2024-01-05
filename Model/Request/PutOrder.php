<?php
declare(strict_types=1);

namespace PiyRibbons\PiyOnline\Model\Request;

use GuzzleHttp\RequestOptions;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use PiyRibbons\PiyOnline\Model\Config;
use PiyRibbons\PiyOnline\Model\PiyClientFactory;
use PiyRibbons\PiyOnline\Observer\Checkout\Cart\UpdateItemsRibbonText;
use PiyRibbons\PiyOnline\Setup\Patch\Data\AddPiyTemplateProductAttribute;
use Psr\Log\LoggerInterface;

class PutOrder extends AbstractRequest
{

    public const ENDPOINT = 'order';
    public const METHOD = 'put';

    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var Order|null
     */
    private ?Order $order = null;

    public function __construct(
        PiyClientFactory $piyClientFactory,
        Json $json,
        LoggerInterface $logger,
        OrderRepositoryInterface $orderRepository,
        Config $config
    ) {
        parent::__construct($piyClientFactory, $json, $logger);
        $this->orderRepository = $orderRepository;
        $this->config = $config;
    }

    /**
     * @param Order $order
     * @return void
     */
    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    protected function getRequestConfig(): array
    {
        if (!$this->order) {
            throw new LocalizedException(__('Please make sure the order is set before trying to push the order.'));
        }

        return [
            RequestOptions::JSON => $this->getRequestBody()
        ];
    }

    /**
     * @return array
     */
    private function getRequestBody()
    {
        $body = [
            'order_reference' => $this->order->getIncrementId(),
            'order_items' => []
        ];

        $defaultTemplate = $this->config->getDefaultTemplate();
        $defaultFont = $this->config->getDefaultFont();

        foreach ($this->order->getItems() as $item) {
            if ($item->getProductType() === Configurable::TYPE_CODE) {
                continue;
            }

            if (!$item->getParentItem()) {
                $buyRequest = $item->getProductOptionByCode('info_buyRequest');
            } else {
                $buyRequest = $item->getParentItem()->getProductOptionByCode('info_buyRequest');
            }

            if (isset($buyRequest[Config::PIY_RIBBON_TEXT_INPUT_NAME]) && $buyRequest[Config::PIY_RIBBON_TEXT_INPUT_NAME]) {
                $body['order_items'][] = [
                    'product_name' => $item->getName(),
                    'product_sku' => $item->getSku(),
                    'product_qty' => $item->getQtyOrdered(),
                    'font_family' => $buyRequest[Config::PIY_RIBBON_FONT_INPUT_NAME] ?? $defaultFont,
                    'print_message' => $buyRequest[Config::PIY_RIBBON_TEXT_INPUT_NAME],
                    'template_id' => $item->getProduct()->getData(AddPiyTemplateProductAttribute::PIY_TEMPLATE_ATTR_CODE) ?? $defaultTemplate
                ];
            }
        }

        return $body;
    }

    /**
     * @param bool $result
     * @param array $response
     * @return void
     */
    public function afterExecute(bool $result, array $response): void
    {
        if ($result === false) {
            $this->logger->error(sprintf(
                'Unable to push order with ID "%s" to PIY Dashboard for printing. Response: %s',
                $this->order->getId(),
                $this->json->serialize($response)
            ));
            $this->order->addCommentToStatusHistory(
                'Something went wrong while pushing the order to the PIY Dashboard.'
            );
            $this->order->addCommentToStatusHistory(
                'PIY Dashboard push error: ' . $this->json->serialize($response)
            );
        } else {
            $this->logger->info(sprintf(
                'Pushed order with ID "%s" to PIY Dashboard for printing. Response: %s',
                $this->order->getId(),
                $this->json->serialize($response)
            ));

            $this->order->setData('pushed_to_piy', 1);
            $this->order->addCommentToStatusHistory(
                'Order has been successfully pushed to the PIY Dashboard'
            );
            $this->orderRepository->save($this->order);
        }
    }


}
