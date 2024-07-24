<?php

namespace Magentoyan\Ticketing\Api\Data;

interface GridInterface {

    
    const ID = 'entity_id';   
    
    const PARENT_ID = 'parent_id';
    
    const CUSTOMER_ID = 'customer_id';  
    
    const ORDER_ID = 'order_id'; 
    
    const STATUS = 'status';  
    
    const WRITTEN_BY = 'written_by'; 
    
    const TITLE = 'title';  
    
    const MESSAGE = 'message'; 
    
    const IMAGE_UPLOAD = 'image_upload';
    
    const CREATED_AT = 'created_at';          
     
    public function getId();

    public function setId($id);
    
    public function getParentId();

    public function setParentId($parentId);

    
    public function getCustomerId();

    public function setCustomerId($customerId);
    

    public function getOrderId();

    public function setOrderId($orderId);
    
    public function getStatus();

    public function setStatus($status);
    
    public function getWrittenBy();

    public function setWrittenBy($writtenBy);
    
    public function getTitle();

    public function setTitle($title);
    
    public function getMessage();

    public function setMessage($message);
    
    public function getImageUpload();

    public function setImageUpload($imageUpload);

    public function getCreatedAt();

    public function setCreatedAt($createdAt);
 
}
