<?php

namespace Magentoyan\Ticketing\Block;

use Magentoyan\Ticketing\Model\Tickets as TicketsModel;

use Magentoyan\Ticketing\Model\Source\StatusOptions;

class History extends \Magento\Framework\View\Element\Template {

    /**
     * @var string
     */
    protected $_template = 'Magentoyan_Ticketing::history.phtml';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    private $_modelTickets;
    
    private $_optionsStatus;

    public function __construct(
            \Magento\Framework\View\Element\Template\Context $context,
            \Magento\Customer\Model\Session $customerSession,
            TicketsModel $modelTickets,
            StatusOptions $optionStatus,
            array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->_modelTickets = $modelTickets;
        $this->_optionsStatus = $optionStatus;
        parent::__construct($context, $data);
    }

    /**
     * @inheritDoc
     */
    protected function _construct() {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Tickets'));
    }
    
    public function getStatusOptions($statusNum)
    {
        $arr = $this->_optionsStatus->toOptionArray();
        
        $key = array_search($statusNum, array_column($arr, 'value'));
        
        return $arr[$key];
    }

    public function getTickets() {

        if (!($customerId = $this->_customerSession->getCustomerId()))
            return false;
        
        $params = $this->getParams();
        
        $item_per_page = 10;
        $page = 1;
        
        if(isset($params['limit']))
            $item_per_page = $params['limit'];
        
        if(isset($params['p']))
            $page = $params['p'];

        $collection = $this->_modelTickets->getCollection()
                ->addFieldToFilter('parent_id', ['null' => true])
                ->addFieldToFilter('customer_id', $customerId)
                ->setOrder('created_at', 'desc')
                ->setPageSize($item_per_page)
                ->setCurPage($page)
                ;

        return $collection;
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout() {
        parent::_prepareLayout();
        if ($this->getTickets()) {
            $pager = $this->getLayout()->createBlock(
                            \Magento\Theme\Block\Html\Pager::class,
                            'tickets.history.pager'
                    )->setCollection($this->getTickets());
            
            $this->setChild('pager', $pager);
            $this->getTickets()->load();
        }
        return $this;
    }

    /**
     * Get Pager child block output
     *
     * @return string
     */
    public function getPagerHtml() {
        return $this->getChildHtml('pager');
    }

    /**
     * Get order view URL
     *
     * @param object $order
     * @return string
     */
    public function getViewUrl($order) {
        return $this->getUrl('sales/order/view', ['order_id' => $order->getId()]);
    }

    /**
     * Get order track URL
     *
     * @param object $order
     * @return string
     * @deprecated 102.0.3 Action does not exist
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getTrackUrl($order) {
        //phpcs:ignore Magento2.Functions.DiscouragedFunction
        trigger_error('Method is deprecated', E_USER_DEPRECATED);
        return '';
    }

    /**
     * Get reorder URL
     *
     * @param object $order
     * @return string
     */
    public function getReorderUrl($order) {
        return $this->getUrl('sales/order/reorder', ['order_id' => $order->getId()]);
    }

    /**
     * Get customer account URL
     *
     * @return string
     */
    public function getBackUrl() {
        return $this->getUrl('customer/account/');
    }
    
    
    public function newTicketUrl()
    {
        return $this->getUrl('ticketing/index/newticket');
    }

    /**
     * Get message for no orders.
     *
     * @return \Magento\Framework\Phrase
     * @since 102.1.0
     */
    public function getEmptyOrdersMessage() {
        return __('You have placed no tickets.');
    }
}
