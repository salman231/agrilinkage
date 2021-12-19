<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */

namespace Sunbrains\SMSProfile\Api\Data;

/**
* @api
*/

interface SMSProfileTemplatesInterface
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
