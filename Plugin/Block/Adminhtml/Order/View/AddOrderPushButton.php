<?php
declare(strict_types=1);

namespace PiyRibbons\PiyOnline\Plugin\Block\Adminhtml\Order\View;

use Magento\Backend\Model\Url;
use Magento\Sales\Block\Adminhtml\Order\View;
use PiyRibbons\PiyOnline\Model\Config;

class AddOrderPushButton
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param View $view
     * @return void
     */
    public function beforeSetLayout(View $view)
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $order = $view->getOrder();

        if (!$order->getData('has_ribbon_text') ||
            $order->getStatus() !== $this->config->getOrderPushStatus()
        ) {
            return;
        }

        $url = $view->getUrl('piyribbons/order/put') . $view->getOrderId();

        $label = __('Push to PIY Dashboard');
        $message = [__('Are you sure you want to push this order to the PIY Dashboard?')];

        if ($order->getData('pushed_to_piy')) {
            $label = __('Re-push to PIY Dashboard');
            $message = [
                __('This order has previously been pushed to the PIY Dashboard'),
                __('Are you sure you want to push this order again?')
            ];
        }

        $view->addButton(
            'order_push_to_piy',
            [
                'label' => $label,
                'class' => 'piy-push-order',
                'onclick' => 'confirmSetLocation("' . implode('<br>', $message) . '", "' . $url . '")'
            ]
        );
    }
}
