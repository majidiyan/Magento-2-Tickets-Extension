<?php

namespace Magentoyan\Ticketing\Model\ResourceModel\Tickets;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    
    protected function _construct()
    {
        $this->_init('Magentoyan\Ticketing\Model\Tickets', 'Magentoyan\Ticketing\Model\ResourceModel\Tickets');
    }

}
