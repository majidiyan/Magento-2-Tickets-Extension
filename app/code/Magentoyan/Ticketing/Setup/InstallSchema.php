<?php

namespace Magentoyan\Ticketing\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface {

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'magentoyan_ticketing'
         */
        $table = $installer->getConnection()
                ->newTable($installer->getTable('magentoyan_ticketing'))
                ->addColumn(
                        'entity_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'PK'
                )
                
                ->addColumn(
                        'parent_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => true, 'default' => null], 'Parent id'
                )
                
                ->addColumn(
                        'customer_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['unsigned' => true], 'Customer Id'
                )
                ->addColumn(
                        'order_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['unsigned' => true, 'default' => '0'], 'Order id'
                )
                ->addColumn(
                        'status',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['unsigned' => false, 'nullable' => false, 'default' => '0'],
                        'Status'
                )
                ->addColumn(
                        'written_by',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        32,
                        ['nullable' => false, 'default' => 'customer'],
                        'Written By'
                )
                ->addColumn(
                        'title',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        1000,
                        ['nullable' => true, 'default' => null],
                        'Title'
                )
                ->addColumn(
                        'message',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        '4k',
                        ['nullable' => true, 'default' => null],
                        'Message'
                )
                ->addColumn(
                        'image_upload',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        1000,
                        ['nullable' => true, 'default' => null],
                        'Image Upload'
                )
                ->addColumn(
                        'created_at',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                        'The date when the activity was created'
                )
                ->addIndex(
                        $installer->getIdxName('magentoyan_ticketing', ['order_id']), ['order_id']
                )
                ->addIndex(
                        $installer->getIdxName('magentoyan_ticketing', ['customer_id']), ['customer_id']
                )
                ->addForeignKey(
                        $installer->getFkName('magentoyan_ticketing', 'customer_id', 'customer_entity', 'entity_id'), 'customer_id', $installer->getTable('customer_entity'), 'entity_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addForeignKey(
                $installer->getFkName('magentoyan_ticketing', 'order_id', 'sales_order', 'entity_id'), 'order_id', $installer->getTable('sales_order'), 'entity_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
        ;
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
