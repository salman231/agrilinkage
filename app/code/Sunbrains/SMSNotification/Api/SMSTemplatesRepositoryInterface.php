<?php
/**
 * Sunbrains
 * Copyright (C) 2019 Sunbrains <info@sunbrains.com>
 *
 * @category  Sunbrains
 * @package   Sunbrains_SMSNotification
 * @author    Sunbrains <info@sunbrains.com>
 * @copyright 2019 Mage Delight (http://www.sunbrains.com/)
 * @license   http://opensource.org/licenses/gpl-3.0.html (GPL-3.0)
 * @link      https://www.sunbrains.com/
 */

namespace Sunbrains\SMSNotification\Api;

/**
* Declare all function defination
*
* @api
*/

use Magento\Framework\Api\SearchCriteriaInterface;
use Sunbrains\SMSNotification\Api\Data\SMSTemplatesInterface;
use Magento\Framework\Exception\NoSuchEntityException;

interface SMSTemplatesRepositoryInterface
{
    /**
     * Function save
     *
     * @param  SMSTemplatesInterface $smsTemplate
     * @return SMSTemplatesInterface
     */
    public function save(SMSTemplatesInterface $smsTemplate);

    /**
     * Function delete
     *
     * @param  SMSTemplatesInterface $smsTemplate
     * @return void
     */
    public function delete(SMSTemplatesInterface $smsTemplate);

    /**
     * Function get By id
     *
     * @param  int $id
     * @return SMSTemplatesInterface
     * @throws NoSuchEntityException
     */
    public function getById($id);

    /**
     * Function get event by type
     *
     * @param  string $eventType
     * @param  string $storeId
     * @return SMSTemplatesInterface
     * @throws NoSuchEntityException
     */
    public function getByEventType($eventType, $storeId);
}
