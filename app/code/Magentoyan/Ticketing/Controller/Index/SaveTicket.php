<?php

namespace Magentoyan\Ticketing\Controller\Index;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magentoyan\Ticketing\Model\Tickets as TicketsModel;


class SaveTicket extends \Magento\Framework\App\Action\Action {

    protected $pageFactory;
    protected $customer_session;
    protected $_orderCollectionFactory;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;
    protected $filesystem;
    protected $fileUploader;
    protected $_store;
    protected $_model;
    
    

    public function __construct(
            Context $context,
            CustomerSession $customerSession,
            \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
            Validator $formKeyValidator,
            Filesystem $filesystem,
            UploaderFactory $fileUploader,
            TicketsModel $ticketsModel,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            
            PageFactory $pageFactory
    ) {
        $this->pageFactory = $pageFactory;
        $this->customer_session = $customerSession;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->formKeyValidator = $formKeyValidator;
        $this->filesystem = $filesystem;
        $this->fileUploader = $fileUploader;

        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);

        $this->_store = $storeManager;

        $this->_model = $ticketsModel;
        
        

        parent::__construct($context);
    }

    public function execute() {

        $page_object = $this->pageFactory->create();

        $this->messageManager->getMessages(true, null);

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $resultRedirect->setPath('/');
            return $resultRedirect;
        }

        if (!$this->customer_session->isLoggedIn()) {
            $resultRedirect->setPath('/');
            return $resultRedirect;
        }

        $page_object->getConfig()->getTitle()->set(__('Save Ticket'));

        $customerId = $this->customer_session->getCustomerId();

        $params = $this->getRequest()->getParams();

        $title = $params['ticket']['title'];
        $message = $params['ticket']['message'];
        $orderId = $params['ticket']['order_id'];

        $imageUrl = null;

        if (!$this->isOrderForCustomer($customerId, $orderId)) {
            $resultRedirect->setPath('/');
            return $resultRedirect;
        }

        $order = $this->getOrder($orderId);
        $incrementId = $order->getIncrementId();

        $file = $this->getRequest()->getFiles('ticket')['image_upload'];

        if (!empty($file['type'])) {

            $thumbnail = $this->uploadFile($file);

            if ($thumbnail[0]) {

                $this->messageManager->addSuccessMessage(__('Image uploaded!'));

                $randStr = $this->randomString();

                $auxArray = explode('.', $thumbnail[3]);
                $fileExtension = $auxArray[count($auxArray) - 1];
                $newFileName = $incrementId . '_' . $randStr . '.' . $fileExtension;

                copy($thumbnail[1], $thumbnail[2] . $newFileName);

                if (file_exists($thumbnail[1]))
                    unlink($thumbnail[1]);

                $thumbnail[1] = $thumbnail[2] . $newFileName;

                $baseUrl = rtrim($this->_store->getStore()->getBaseUrl(), '/');

                $auxArray = explode('/pub/', $thumbnail[1]);

                $imageUrl = $baseUrl . '/pub/' . $auxArray[1];
            } else {
                $this->messageManager->addErrorMessage(__('Only Image files allowed to send!'));
                $resultRedirect->setPath('ticketing/index/newticket');
                return $resultRedirect;
            }
        }


        $this->_model->setData([
            'customer_id' => $customerId,
            'order_id' => $orderId,
            'title' => $title,
            'message' => $message,
        ]);

        if (!is_null($imageUrl)) {
            $this->_model->addData([
                'image_upload' => $imageUrl,
            ]);
        }
        
        $this->_model->save();
        
        
        
        $this->messageManager->addSuccessMessage(__('Your ticket successfuly saved !'));
        $resultRedirect->setPath('ticketing/index/history');
        return $resultRedirect;

        return $page_object;
    }

    private function uploadFile($file) {


        $yourFolderName = 'wysiwyg/ticketing/';
        $yourInputFileName = 'ticket[image_upload]';

        try {

            $fileName = ($file && array_key_exists('name', $file)) ? $file['name'] : null;

            if ($file && $fileName) {
                $target = $this->mediaDirectory->getAbsolutePath($yourFolderName);

                /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
                $uploader = $this->fileUploader->create(['fileId' => $yourInputFileName]);

                // set allowed file extensions
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);

                // allow folder creation
                $uploader->setAllowCreateFolders(true);

                // rename file name if already exists 
                $uploader->setAllowRenameFiles(true);

                // upload file in the specified folder
                $result = $uploader->save($target);

                if ($result['file']) {
                    return [true, $target . $uploader->getUploadedFileName(), $target, $uploader->getUploadedFileName()];
                } else
                    return [false, 'error upload'];
            }
        } catch (\Exception $e) {
            return [false, $e->getMessage()];
        }
    }

    private function isOrderForCustomer($customerId, $orderId) {
        $count = $this->_orderCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', $orderId)
                ->addFieldToFilter('customer_id', $customerId)
                ->count();

        return $count >= 1;
    }
    
    private function isDuplicated($customerId, $orderId) {
        $count = $this->_model->getCollection()
                ->addFieldToFilter('order_id', $orderId)
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('parent_id', ['null' => true])
                ->count();

        return $count >= 1;
    }

    private function getOrder($orderId) {
        $order = $this->_orderCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', $orderId)
                ->getLastItem();

        return $order;
    }

    private function randomString() {
        $n = 8;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }
}
