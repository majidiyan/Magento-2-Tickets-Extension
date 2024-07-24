<?php
 
    /**
     * Magentoyan Grid collection
     *
     * @category    Magentoyan
     * @package     Magentoyan_Ticketing
     * @author      Magentoyan Software Private Limited
     *
     */
 
namespace Magentoyan\Ticketing\Model\ResourceModel\Grid;
 
/* use required classes */
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';
 
    /**
     * @param EntityFactoryInterface $entityFactory,
     * @param LoggerInterface        $logger,
     * @param FetchStrategyInterface $fetchStrategy,
     * @param ManagerInterface       $eventManager,
     * @param StoreManagerInterface  $storeManager,
     * @param AdapterInterface       $connection,
     * @param AbstractDb             $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->_init('Magentoyan\Ticketing\Model\Grid', 'Magentoyan\Ticketing\Model\ResourceModel\Grid');
        //Class naming structure 
        // 'NameSpace\ModuleName\Model\ModelName', 'NameSpace\ModuleName\Model\ResourceModel\ModelName'
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeManager = $storeManager;
    }
     
    protected function _initSelect()
    {
        parent::_initSelect();
        
        $this->addFilterToMap('increment_id', 'thirdTable.increment_id');
        $this->addFilterToMap('status', 'main_table.status');
        $this->addFilterToMap('created_at', 'main_table.created_at');
        $this->addFilterToMap('entity_id', 'main_table.entity_id');
        
        $this->getSelect()->joinLeft(
                        ['secondTable' => $this->getTable('customer_entity')],
                        'main_table.customer_id = secondTable.entity_id',
                        ['firstname', 'lastname']
                )
                ->joinLeft(
                ['thirdTable' => $this->getTable('sales_order')],
                'main_table.order_id = thirdTable.entity_id',
                ['increment_id']
            );
        
            $this->addFieldToFilter('parent_id', ['null' => true]);
 
        $this->getSelect();
    }
}