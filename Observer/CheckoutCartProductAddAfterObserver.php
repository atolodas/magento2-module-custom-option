<?php

namespace Qooar\CustomOption\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class CheckoutCartProductAddAfterObserver implements ObserverInterface {

    protected $_request;

    public function __construct(RequestInterface $request) {
        $this->_request = $request;
    }

    public function execute(EventObserver $observer) {

        $item = $observer->getQuoteItem();

        $additionalOptions = array();

        if ($additionalOption = $item->getOptionByCode('additional_options')) {
            $additionalOptions = (array) unserialize($additionalOption->getValue());
        }

        $post = $this->_request->getParam('custom_options');

        if (is_array($post)) {
            foreach ($post as $key => $value) {
                if ($key == '' || $value == '') {
                    continue;
                }

                $additionalOptions[] = [
                    'label' => $key,
                    'value' => $value
                ];
            }
        }

        if (count($additionalOptions) > 0) {
            $item->addOption(array(
                'code' => 'additional_options',
                'value' => serialize($additionalOptions)
            ));
        }
    }

}
