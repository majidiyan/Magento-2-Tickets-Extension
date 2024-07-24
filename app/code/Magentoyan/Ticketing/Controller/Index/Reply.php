<?php

namespace Magentoyan\Ticketing\Controller\Index;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magentoyan\Ticketing\Model\Tickets as TicketsModel;
use Magento\Framework\Data\Form\FormKey\Validator;

class Reply extends \Magento\Framework\App\Action\Action {

    protected $pageFactory;
    protected $customer_session;
    protected $formKeyValidator;
    protected $_store;
    protected $_model;

    public function __construct(
            Context $context,
            CustomerSession $customerSession,
            Validator $formKeyValidator,
            TicketsModel $ticketsModel,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            PageFactory $pageFactory
    ) {
        $this->pageFactory = $pageFactory;
        $this->customer_session = $customerSession;
        $this->formKeyValidator = $formKeyValidator;
        $this->_store = $storeManager;
        $this->_model = $ticketsModel;

        parent::__construct($context);
    }

    public function execute() {
        $page_object = $this->pageFactory->create();

        $this->messageManager->getMessages(true, null);

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if (!$this->customer_session->isLoggedIn()) {
            $resultRedirect->setPath('/');
            return $resultRedirect;
        }

        $params = $this->getRequest()->getParams();

        $ticketId = $params['ticket_id'];

        $customerId = $this->customer_session->getCustomerId();

        if (!$this->isTicketForCustomer($customerId, $ticketId)) {

            $resultRedirect->setPath('/');
            return $resultRedirect;
        }

        $page_object->getConfig()->getTitle()->set(__('Reply'));

        if ($this->getRequest()->getPostValue()) {
            
            if (!$this->formKeyValidator->validate($this->getRequest())) {
                $resultRedirect->setPath('/');
                return $resultRedirect;
            }
            
            
            
            //create new record
            $parentRow = $this->_model->load($ticketId);
            $parentRowOrderId = $parentRow->getData('order_id');
            $parentRowCustomerId = $parentRow->getData('customer_id');
            
            $this->_model->setData([
                'parent_id' => $ticketId,
                'order_id' => $parentRowOrderId,
                'customer_id' => $parentRowCustomerId,
                'message' => $params['ticket']['reply_message'],
                
            ])->save();
            //create new record end
            
            //update parent record
            $parentRow->setData(['status' => 1]);
            $parentRow->setId($ticketId);
            $parentRow->save();
            //update parent record end
            
            $this->messageManager->addSuccessMessage(__('Your reply is sent!'));
        }



        //magentoyan.ticketing.reply

        $page_object->getLayout()->getBlock('magentoyan.ticketing.reply')->setTicketId($ticketId);

        return $page_object;
    }

    private function isTicketForCustomer($customerId, $ticketId) {

        $count = $this->_model->getCollection()
                ->addFieldToFilter('entity_id', $ticketId)
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('parent_id', ['null' => true])
                ->count();

        return $count >= 1;
    }
}
