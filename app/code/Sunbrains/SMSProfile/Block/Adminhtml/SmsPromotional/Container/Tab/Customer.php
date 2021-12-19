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
use Magento\Customer\Model\CustomerFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as BackendHelper;

class Customer extends Extended
{
    /**
     * @var CustomerFactory
     */
    private $customer;

    /**
     *  constructor.
     *
     * @param Context  $context
     * @param BackendHelper $backendHelper
     * @paramCustomerFactory $customer
     * @param array    $data
     */

    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        CustomerFactory $customer,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->customer = $customer;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('smspromotional_customer');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    /**
     * @return Grid
     */
    protected function _prepareCollection()
    {      
        $collection = $this->customer->create()->getCollection();
        $this->setCollection($collection); 
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        
        $this->addColumn(
            'selected_customer',
            [
                'type'              => 'checkbox',
                'name'              => 'selected_customer',
                'field_name'        => 'selectedcustomer[]',
                'values'            => ['1'],
                'index'             => 'id',
                'header_css_class'  => 'col-select col-massaction',
                'column_css_class'  => 'col-select col-massaction'
            ]
        );
        $this->addColumn(
            'entity_id',
            [
                'header'           => __('ID'),
                'sortable'         => true,
                'type'             => 'number', /*apply filter in range*/
                'index'            => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn('firstname', ['header' => __('First Name' ), 'index' => 'firstname']);
        $this->addColumn('lastname', ['header' => __('Last Name'), 'index' => 'lastname']);
        $this->addColumn('email', ['header' => __('Email1'), 'index' => 'email']); 
        
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('smsprofile/smspromotional/customergrid', ['_current' => true]);
    }

    /* remove click event on grid row */
    public function getRowUrl($row)
    {
        return false;
    }
}
