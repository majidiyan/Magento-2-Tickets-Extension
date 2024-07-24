<?php

namespace Magentoyan\Ticketing\Block;

use Magento\Framework\View\Element\Template;
use Magentoyan\Ticketing\Model\Tickets as TicketsModel;

class Main extends Template {

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $_customerSession;
    private $_modelTickets;
    private $_orderCollectionFactory;

    public function __construct(
            \Magento\Framework\View\Element\Template\Context $context,
            \Magento\Customer\Model\Session $customerSession,
            TicketsModel $modelTickets,
            \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
            array $data = []
    ) {

        $this->_customerSession = $customerSession;
        $this->_modelTickets = $modelTickets;
        $this->_orderCollectionFactory = $orderCollectionFactory;

        parent::__construct($context, $data);
    }

    protected function _prepareLayout() {

        parent::_prepareLayout();
    }

    public function getCustomerId() {
        return $this->_customerSession->getCustomerId();
    }

    public function getCustomerOrdersIds($customerId) {
        
        $result = [];
        
        $collection = $this->_orderCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('customer_id', $customerId)
                ->setOrder('created_at', 'desc')
                ;
        
        foreach($collection as $order)
            $result[$order->getId()] = $order->getIncrementId();
        
        return $result;
    }
    
    public function saveTicketUrl()
    {
        return $this->getUrl('ticketing/index/saveticket', ['_secure' => true]);
    }
    
    public function getHistoryCollection($ticketId) {
        
        $collection = $this->_modelTickets->getCollection();
        
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
