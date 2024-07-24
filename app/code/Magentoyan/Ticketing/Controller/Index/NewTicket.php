<?php

namespace Magentoyan\Ticketing\Controller\Index;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\Session as CustomerSession;

class NewTicket extends \Magento\Framework\App\Action\Action {

    protected $pageFactory;
    
    protected $customer_session;

    public function __construct(
            Context $context, 
            CustomerSession $customerSession,
            PageFactory $pageFactory
            ) {
        $this->pageFactory = $pageFactory;
        $this->customer_session = $customerSession;
        parent::__construct($context);
    }

    public function execute() {
        $page_object = $this->pageFactory->create();
        
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        
        if (!$this->customer_session->isLoggedIn()) {
            $resultRedirect->setPath('/');
            return $resultRedirect;
        }
        
        $page_object->getConfig()->getTitle()->set(__('New Ticket'));
        
        
        return $page_object;
    }
}
