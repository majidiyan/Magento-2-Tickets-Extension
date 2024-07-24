<?php

/**
 * Magentoyan_Ticketing Add New Row Form Admin Block.
 * @category    Magentoyan
 * @package     Magentoyan_Ticketing
 * @author      Magentoyan Software Private Limited
 *
 */

namespace Magentoyan\Ticketing\Block\Adminhtml\Grid\Edit;

/**
 * Adminhtml Add New Row Form.
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic {

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context,
     * @param \Magento\Framework\Registry $registry,
     * @param \Magento\Framework\Data\FormFactory $formFactory,
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
     * @param \Magentoyan\Ticketing\Model\Status $options,
     */
    public function __construct(
            \Magento\Backend\Block\Template\Context $context,
            \Magento\Framework\Registry $registry,
            \Magento\Framework\Data\FormFactory $formFactory,
            \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
            /* \Magentoyan\Ticketing\Model\Status $options, */
            array $data = []
    ) {
        /* $this->_options = $options; */
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm() {
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);

        $model = $this->_coreRegistry->registry('row_data');
        $form = $this->_formFactory->create(
                ['data' => [
                        'id' => 'edit_form',
                        'enctype' => 'multipart/form-data',
                        'action' => $this->getData('action'),
                        'method' => 'post'
                    ]
                ]
        );

        $form->setHtmlIdPrefix('magentoyancogrid_');
        if ($model->getId()) {
            $fieldset = $form->addFieldset(
                    'base_fieldset',
                    ['legend' => __('Edit Row Data'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('entity_id', 'hidden', ['name' => 'id']);
        } else {
            $fieldset = $form->addFieldset(
                    'base_fieldset',
                    ['legend' => __('Add Row Data'), 'class' => 'fieldset-wide']
            );
        }


        
        
        
        $fieldset->addField('note', 'note', array(
            'text' => $this->getLayout()->createBlock('\Magentoyan\Ticketing\Block\Adminhtml\Grid\Edit\Form\History')
            ->setRowModel($model)          
            ->toHtml(),
        ));

        

        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

        $fieldset->addField(
                'message_reply',
                'editor',
                [
                    'name' => 'message_reply',
                    'label' => __('Reply'),
                    'style' => 'height:36em;',
                    'required' => true,
                    'config' => $wysiwygConfig
                ]
        );

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
