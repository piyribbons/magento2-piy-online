<?php
declare(strict_types=1);

namespace PiyRibbons\PiyOnline\Observer\Order\Place;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use PiyRibbons\PiyOnline\Model\Config;
use PiyRibbons\PiyOnline\Observer\Checkout\Cart\UpdateItemsRibbonText;

class FlagOrderWithRibbonText implements ObserverInterface
{
    private OrderRepositoryInterface $orderRepository;

    /**
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }


    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getData('order');
        $orderItems = $order->getItems();

        foreach ($orderItems as $orderItem) {
            $buyRequest = $orderItem->getProductOptionByCode('info_buyRequest') ?? [];
            if (isset($buyRequest[Config::PIY_RIBBON_TEXT_INPUT_NAME]) && $buyRequest[Config::PIY_RIBBON_TEXT_INPUT_NAME]) {
                $order->setData('has_ribbon_text', 1);
                $this->orderRepository->save($order);
                break;
            }
        }
    }
}
