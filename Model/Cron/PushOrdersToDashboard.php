<?php

namespace PiyRibbons\PiyOnline\Model\Cron;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\OrderRepositoryInterface;
use PiyRibbons\PiyOnline\Model\Config;
use PiyRibbons\PiyOnline\Model\Request\PutOrder;
use Psr\Log\LoggerInterface;

class PushOrdersToDashboard
{
    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var PutOrder
     */
    private PutOrder $putOrder;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param PutOrder $putOrder
     * @param Config $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PutOrder $putOrder,
        Config $config,
        LoggerInterface $logger
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->putOrder = $putOrder;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        if (!$this->config->isEnabled()) {
            $this->logger->info('The PIY Ribbons plugin is disabled. Please enable it to push orders.');
        }

        $this->logger->info('Starting PIY Dashboard order push cron.');

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('has_ribbon_text', 1)
            ->addFilter('pushed_to_piy', 0)
            ->addFilter('status', $this->config->getOrderPushStatus());


        $ordersToPush = $this->orderRepository->getList($searchCriteria->create());

        if ($ordersToPush->getTotalCount()) {
            $this->logger->info(
                sprintf(
                    'Found %s orders to push to the PIY Dashboard',
                    $ordersToPush->getTotalCount()
                )
            );
        } else {
            $this->logger->info('No orders pending to be pushed to the PIY Dashboard');
            return;
        }

        foreach ($ordersToPush->getItems() as $order) {
            $this->putOrder->setOrder($order);
            $this->putOrder->execute();
        }

        $this->logger->info('Finished pushing orders to the PIY Dashboard');
    }
}
