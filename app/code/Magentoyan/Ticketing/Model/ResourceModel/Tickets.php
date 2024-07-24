<?php

namespace Magentoyan\Ticketing\Model\ResourceModel;

class Tickets extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    protected function _construct() {
        $this->_init('magentoyan_ticketing', 'entity_id');
    }

}
