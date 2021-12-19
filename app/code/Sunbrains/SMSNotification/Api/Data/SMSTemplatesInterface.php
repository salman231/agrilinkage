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
 
namespace Sunbrains\SMSNotification\Api\Data;

/**
* @api
*/

interface SMSTemplatesInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return void
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getTemplateName();

    /**
     * @param string $templateName
     *
     * @return void
     */
    public function setTemplateName($templateName);


    /**
     * @return string
     */
    public function getTemplateContent();

    /**
     * @param string $templateContent
     *
     * @return void
     */
    public function setTemplateContent($templateContent);

    /**
     * @return string
     */
    public function getEventType();

    /**
     * @param string $eventType
     *
     * @return void
     */
    public function setEventType($eventType);
    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     *
     * @return void
     */
    public function setStoreId($storeId);
    
}
