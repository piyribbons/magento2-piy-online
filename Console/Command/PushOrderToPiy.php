<?php
declare(strict_types=1);

namespace PiyRibbons\PiyOnline\Console\Command;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\OrderRepositoryInterface;
use PiyRibbons\PiyOnline\Model\Config;
use PiyRibbons\PiyOnline\Model\Request\PutOrder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PushOrderToPiy extends Command
{
    private const ORDERS_OPTION = 'orders';

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
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param PutOrder $putOrder
     * @param Config $config
     * @param string|null $name
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PutOrder $putOrder,
        Config $config,
        string $name = null
    ) {
        parent::__construct($name);
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->putOrder = $putOrder;
        $this->config = $config;
    }


    /**
     * Initialization of the command.
     */
    protected function configure()
    {
        $this->setName('piy:push:orders');
        $this->setDescription('Push the given orders to the PIY Dashboard');

        $this->addArgument(
            self::ORDERS_OPTION,
            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
            'Optional: Orders IDs of the orders which should be pushed to the PIY dashboard. Multiple order IDs can be added by seperating them with a space character'
        );

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->config->isEnabled()) {
            $output->writeln('<info>The PIY Ribbons plugin is disabled. Please enable it to push orders.</info>');
        }

        $orderIds = $input->getArgument(self::ORDERS_OPTION);
        $output->writeln('<info>Pushing orders to PIY Dashboard</info>');

        $searchCriteria = $this->searchCriteriaBuilder->addFilter('has_ribbon_text', 1);

        if ($orderIds) {
            $searchCriteria->addFilter('entity_id', $orderIds, 'in');
            $output->writeln('<info>Limited to orders with IDs ' . implode(', ', $orderIds) . '</info>');
        }

        $ordersToPush = $this->orderRepository->getList($searchCriteria->create());

        foreach ($ordersToPush->getItems() as $order) {
            $this->putOrder->setOrder($order);
            $this->putOrder->execute();
        }

        return 0;
    }
}
