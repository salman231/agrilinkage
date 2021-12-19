<?php
/**
 * Sunbrains
 * Copyright (C) 2019 Sunbrains <info@sunbrains.com>
 *
 * @category Sunbrains
 * @package Sunbrains_SMSNotification
 * @copyright Copyright (c) 2019 Mage Delight (http://www.sunbrains.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Sunbrains <info@sunbrains.com>
 */
 
namespace Sunbrains\SMSNotification\Controller\Adminhtml\SmsTemplates;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Sunbrains\SMSNotification\Model\SMSTemplatesFactory;
use Sunbrains\SMSNotification\Api\SMSTemplatesRepositoryInterface;

class Edit extends Action
{
    /**
     * @var string
     */
    const ACTION_RESOURCE = 'Sunbrains_SMSNotification::smstemplates';

    /**
     * SmsTemplates  factory
     *
     * @var SMSTemplatesFactory
     */
    private $smsTemplates;

    /**
     * SMSTemplatesRepositoryInterface
     *
     * @var SMSTemplatesFactory
     */
    private $smsTemplatesRepository;

    /**
     * Core registry
     *
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * DataPersistorInterface
     *
     * @var dataPersistor
     */
    private $dataPersistor;

    /**
     * @param Registry $registry
     * @param DataPersistorInterface $dataPersistor,
     * @param SMSTemplatesFactory $smsTemplates
     * @param SMSTemplatesRepositoryInterface $smsTemplatesRepository
     * @param PageFactory $resultPageFactory
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        SMSTemplatesFactory $smsTemplates,
        SMSTemplatesRepositoryInterface $smsTemplatesRepository,
        DataPersistorInterface $dataPersistor,
        PageFactory $resultPageFactory,
        Context $context
    ) {
        $this->coreRegistry      = $registry;
        $this->dataPersistor = $dataPersistor;
        $this->smsTemplates  = $smsTemplates;
        $this->smsTemplatesRepository  = $smsTemplatesRepository;
        $this->resultPageFactory = $resultPageFactory;        
        parent::__construct($context);
    }

    private function _initSmsTemplates()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $resultPage->setActiveMenu('Sunbrains_SMSNotification::smsnotification');
        $resultPage->addBreadcrumb(__('Sunbrains'), __('Sunbrains'));
        $resultPage->addBreadcrumb(__('SmsTemplates'), __('SmsTemplates'));
        
        return $resultPage;
    }

     protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ACTION_RESOURCE);
    }

     /**
     * Execute action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('entity_id');
        $smsTemplate ='';
        if ($id) {
            $smsTemplate = $this->smsTemplatesRepository->getById($id);

            if (!$smsTemplate->getId()) {
                $this->messageManager->addError(__('This Templates no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirect->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        if ($smsTemplate) {
            $this->dataPersistor->set('smstemplates',$smsTemplate);
        }

        $resultPage = $this->_initSmsTemplates();

        $resultPage->addBreadcrumb(
            $id ? __('Edit SmsTemplates') : __('New SmsTemplates'),
            $id ? __('Edit SmsTemplates') : __('New SmsTemplates')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('New SmsTemplates'));
        if ($smsTemplate) {
            $resultPage->getConfig()->getTitle()
                ->prepend($smsTemplate->getTemplateName() ? $smsTemplate->getTemplateName() : __('New SmsTemplates'));
        }

        return $resultPage;
    }
}