<?php

namespace Magentoyan\Ticketing\Controller\Index;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

class History extends \Magento\Framework\App\Action\Action {

    protected $pageFactory;

    public function __construct(Context $context, PageFactory $pageFactory) {
        $this->pageFactory = $pageFactory;
        parent::__construct($context);
    }

    public function execute() {
        $page_object = $this->pageFactory->create();
        
        $params = $this->getRequest()->getParams();
        
        $page_object->getConfig()->getTitle()->set(__('My Tickets'));
        
        $page_object->getLayout()->getBlock('magentoyan.ticketing.history')->setParams($params);
        
        return $page_object;
    }
}
