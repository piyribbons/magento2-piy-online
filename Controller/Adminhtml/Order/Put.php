<?php

namespace PiyRibbons\PiyOnline\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Sales\Api\OrderRepositoryInterface;
use PiyRibbons\PiyOnline\Model\Request\PutOrder;

class Put extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'PiyRibbons_PiyOnline::put_order';

    /**
     * @var PutOrder
     */
    private PutOrder $putOrder;

    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * @param Context $context
     * @param PutOrder $putOrder
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Context $context,
        PutOrder $putOrder,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct($context);
        $this->putOrder = $putOrder;
        $this->orderRepository = $orderRepository;
    }


    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface|ResponseInterface
     * @throws NotFoundException
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = $this->orderRepository->get($orderId);

        $this->putOrder->setOrder($order);
        $this->putOrder->execute();

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)
            ->setUrl($this->_redirect->getRefererUrl());
    }
}
