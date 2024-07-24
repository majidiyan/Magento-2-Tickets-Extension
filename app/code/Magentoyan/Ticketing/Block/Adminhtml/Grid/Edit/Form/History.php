<?php

namespace Magentoyan\Ticketing\Block\Adminhtml\Grid\Edit\Form;

use Magentoyan\Ticketing\Model\Tickets;

class History extends \Magento\Backend\Block\Template {

    protected $_template = 'Magentoyan_Ticketing::history.phtml';
    protected $_model;

    public function __construct(
            \Magento\Backend\Block\Template\Context $context,
            Tickets $tickets,
            array $data = []
    ) {
        $this->_model = $tickets;
        parent::__construct($context, $data);
    }

    public function getThisModel() {
        return $this->getRowModel();
    }

    public function getHistoryCollection($ticketId) {
        
        $collection = $this->_model->getCollection();
        
        $collection->addFilterToMap('increment_id', 'thirdTable.increment_id');
        $collection->addFilterToMap('status', 'main_table.status');
        $collection->addFilterToMap('created_at', 'main_table.created_at');
        $collection->addFilterToMap('entity_id', 'main_table.entity_id');
        
        $collection->addFieldToFilter(['entity_id', 'parent_id'], [$ticketId, $ticketId])
                ;

        $collection->addFilterToMap('increment_id', 'thirdTable.increment_id');
        $collection->addFilterToMap('status', 'main_table.status');
        $collection->addFilterToMap('created_at', 'main_table.created_at');
        $collection->addFilterToMap('entity_id', 'main_table.entity_id');

        $collection->getSelect()->joinLeft(
                ['secondTable' => $collection->getTable('customer_entity')],
                'main_table.customer_id = secondTable.entity_id',
                ['firstname', 'lastname']
        )->joinLeft(
                ['thirdTable' => $collection->getTable('sales_order')],
                'main_table.order_id = thirdTable.entity_id',
                ['increment_id']
        );
        
        $collection->getSelect()->order("main_table.created_at ASC");


        return $collection;
    }
}
