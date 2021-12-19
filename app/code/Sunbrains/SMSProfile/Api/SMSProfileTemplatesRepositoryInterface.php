<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */

namespace Sunbrains\SMSProfile\Api;

/**
* @api
*/

use Magento\Framework\Api\SearchCriteriaInterface;
use Sunbrains\SMSProfile\Api\Data\SMSProfileTemplatesInterface;
use Magento\Framework\Exception\NoSuchEntityException;

interface SMSProfileTemplatesRepositoryInterface
{
    /**
     * function 
     *
     * @param  SMSProfileTemplatesInterface $smsTemplate
     * @return SMSProfileTemplatesInterface
     */

    public function save(SMSProfileTemplatesInterface $smsTemplate);

    /**
     * function 
     *
     * @param SMSProfileTemplatesInterface $smsTemplate
     * @return void
     */
    public function delete(SMSProfileTemplatesInterface $smsTemplate);

    /**
     * function 
     *
     * @param int $id
     * @return SMSProfileTemplatesInterface
     * @throws NoSuchEntityException
     */

    public function getById($id);

    /**
     * function 
     *
     * @param string $eventType
     * @param string $storeId
     * @return SMSProfileTemplatesInterface
     * @throws NoSuchEntityException
     */

    public function getByEventType($eventType, $storeId);
}
