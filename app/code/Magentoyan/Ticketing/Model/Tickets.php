<?php

namespace Magentoyan\Ticketing\Model;


class Tickets extends \Magento\Framework\Model\AbstractModel {

     protected function _construct() {
        $this->_init('Magentoyan\Ticketing\Model\ResourceModel\Tickets');
    }

}
