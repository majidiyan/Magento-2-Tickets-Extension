<?php

/**
 * Grid Admin Cagegory Map Record Save Controller.
 * @category  Magentoyan
 * @package   Magentoyan_Ticketing
 * @author    Magentoyan
 * @copyright Copyright (c) 2010-2016 Magentoyan Software Private Limited (https://magentoyan.com)
 * @license   https://store.magentoyan.com/license.html
 */
namespace Magentoyan\Ticketing\Controller\Adminhtml\Grid;

use Magentoyan\Ticketing\Model\Tickets as TicketsModel;


class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magentoyan\Ticketing\Model\GridFactory
     */
    var $gridFactory;
    
    
    protected $_model;
    
    protected $authSession;
    
    
    

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magentoyan\Ticketing\Model\GridFactory $gridFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
            
            TicketsModel $ticketsModel,
            \Magento\Backend\Model\Auth\Session $authSession,
            
            
        \Magentoyan\Ticketing\Model\GridFactory $gridFactory
    ) {
        parent::__construct($context);
        $this->gridFactory = $gridFactory;
        
        $this->_model = $ticketsModel;
        
        $this->authSession = $authSession;
        
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('ticketing/grid/addrow');
            return;
        }
        try {
            $rowData = $this->gridFactory->create();
            
            $username = $this->authSession->getUser()->getUsername();
            
            $currentRow = $this->_model->load($data['id']);
            
            $replyToPk = $currentRow->getId();
            $replyToOrderId = $currentRow->getData('order_id');
            $replyToCustomerId = $currentRow->getData('customer_id');
            
            $rowReply = $this->gridFactory->create();
            $rowReply->setData([
                'parent_id' => $replyToPk,
                'order_id' => $replyToOrderId,
                'customer_id' => $replyToCustomerId,
                'message' => $data['message_reply'],
                'written_by' => $username,
            ])->save();
            
            $rowData->setData(['status' => 2]);
            if (isset($data['id'])) {
                $rowData->setId($data['id']);
            }
            $rowData->save();
            
            
            
            $this->messageManager->addSuccess(__('Row data has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('ticketing/grid/addrow' , ['id' => $replyToPk]);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magentoyan_Ticketing::save');
    }
}
