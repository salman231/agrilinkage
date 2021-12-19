<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */

namespace Sunbrains\SMSProfile\Block\Adminhtml\SmsPromotional\Edit\Buttons;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\NoSuchEntityException;

class Generic
{

    /**
     * @var Context
     */
    private $context;
    
    /**
     * @param Context $context
     * @param AuthorRepositoryInterface $authorRepository
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;        
    }
    
    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
