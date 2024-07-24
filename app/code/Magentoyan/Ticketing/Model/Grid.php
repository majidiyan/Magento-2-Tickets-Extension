<?php

namespace Magentoyan\Ticketing\Model;

use Magentoyan\Ticketing\Api\Data\GridInterface;

class Grid extends \Magento\Framework\Model\AbstractModel implements GridInterface
{
    /**
     * CMS page cache tag.
     */
    const CACHE_TAG = 'magentoyan_ticketing';

    /**
     * @var string
     */
    protected $_cacheTag = 'magentoyan_ticketing';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'magentoyan_ticketing';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('Magentoyan\Ticketing\Model\ResourceModel\Grid');
    }
    /**
     * Get EntityId.
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

   
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }
    
    
    public function getParentId()
    {
        return $this->getData(self::PARENT_ID);
    }

    public function setParentId($parentId)
    {
        return $this->setData(self::PARENT_ID, $parentId);
    }

    
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }
    
    
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }
    
   
    public function getStatus(){
        return $this->getData(self::STATUS);
    }

    public function setStatus($status){
        return $this->setData(self::STATUS, $status);
    }
    
    public function getWrittenBy(){
        return $this->getData(self::WRITTEN_BY);
    }

    public function setWrittenBy($writtenBy){
        return $this->setData(self::WRITTEN_BY, $writtenBy);
    }

    public function getTitle(){
        return $this->getData(self::TITLE);
    }

    public function setTitle($title){
        return $this->setData(self::TITLE, $title);
    }

    public function getMessage(){
        return $this->getData(self::MESSAGE);
    }

    public function setMessage($message){
        return $this->setData(self::MESSAGE, $message);
    }


    public function getImageUpload(){
        return $this->getData(self::IMAGE_UPLOAD);
    }

    public function setImageUpload($imageUpload){
        return $this->setData(self::IMAGE_UPLOAD, $imageUpload);
    }

    public function getCreatedAt(){
        return $this->getData(self::CREATED_AT);
    }

    public function setCreatedAt($createdAt){
        return $this->setData(self::CREATED_AT, $createdAt);
    }
    
    
}
