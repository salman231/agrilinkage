<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */

namespace Sunbrains\SMSProfile\Block\Adminhtml\SmsPromotional\Container\Tab;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Customer\Model\GroupFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as BackendHelper;

class CustomerGroup extends Extended
{
    /**
    * @var GroupFactory
    */
    private $groupFactory;

    /**
     * constructor.
     *
     * @param Context  $context
     * @param BackendHelper $backendHelper
     * @param GroupFactory $groupFactory
     * @param array    $data
     */
    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        GroupFactory $groupFactory,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->groupFactory = $groupFactory;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('smspromotional_customergroup');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    /**
     * @return Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->groupFactory->create()->getCollection();
        $collection->addFieldToFilter('customer_group_code',['neq'=>'NOT LOGGED IN']);
        $this->setCollection($collection); 
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        
        $this->addColumn(
            'selected_customer_group',
            [
                'type'                => 'checkbox',
                'name'                => 'selected_customer_group',
                'field_name'          => 'selectedcustomergroup[]',
                'values'              => ['1'],
                'index'               => 'selected_customer_group',
                'header_css_class'    => 'col-select col-massaction',
                'column_css_class'    => 'col-select col-massaction'
            ]
        );
        $this->addColumn(
            'customer_group_id',
            [
                'header'           => __('ID'),
                'sortable'         => true,
                'type'             => 'number', /*apply filter in range*/
                'index'            => 'customer_group_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn('customer_group_code', ['header' => __('Customer Group Code' ), 'index' => 'customer_group_code']);
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('smsprofile/smspromotional/customergroupgrid', ['_current' => true]);
    }

    /* remove click event on grid row */
    public function getRowUrl($row)
    {
        return false;
    }
}

