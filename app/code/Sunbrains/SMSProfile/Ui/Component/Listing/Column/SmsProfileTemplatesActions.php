<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */
namespace Sunbrains\SMSProfile\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class SmsProfileTemplatesActions extends Column
{
    /**
     * Url path  to edit
     * 
     * @var string
     */
    const URL_PATH_EDIT = 'smsprofile/smsprofiletemplates/edit';
    /**
     * Url path  to delete
     * 
     * @var string
     */
    const URL_PATH_DELETE = 'smsprofile/smsprofiletemplates/delete';
    /**
     * URL builder
     * 
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;
    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    )
    {
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['entity_id'])) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->_urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    'entity_id' => $item['entity_id']
                                ]
                            ),
                            'label' => __('Edit')
                        ],
                        'delete' => [
                            'href' => $this->_urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    'entity_id' => $item['entity_id']
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete "${ $.$data.template_name  }"'),
                                'message' => __('Are you sure you want to delete the Template "${ $.$data.template_name  }" ?')
                            ]
                        ]
                    ];
                }
            }
        }
        return $dataSource;
    }
}
