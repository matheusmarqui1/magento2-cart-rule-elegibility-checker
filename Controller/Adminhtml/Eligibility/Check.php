<?php
declare(strict_types=1);

namespace Ytec\RuleEligibilityCheck\Controller\Adminhtml\Eligibility;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Ytec\RuleEligibilityCheck\Api\RuleEligibilityCheckInterface;

/**
 * Class Check
 *
 * Admin controller to check the eligibility of a customer against a Sales Rule.
 *
 * @package Ytec\RuleEligibilityCheck\Controller\Adminhtml\Eligibility
 */
class Check extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Ytec_RuleEligibilityCheck::eligibility_check';

    /**
     * @var RuleEligibilityCheckInterface
     */
    private RuleEligibilityCheckInterface $ruleEligibilityCheck;

    /**
     * Check constructor.
     *
     * @param RuleEligibilityCheckInterface $ruleEligibilityCheck Service to check rule eligibility.
     * @param Context $context Controller context.
     */
    public function __construct(RuleEligibilityCheckInterface $ruleEligibilityCheck, Context $context)
    {
        parent::__construct($context);
        $this->ruleEligibilityCheck = $ruleEligibilityCheck;
    }

    /**
     * Executes the controller action to check rule eligibility.
     *
     * Fetches customer and rule IDs from the request parameters and performs
     * the eligibility check, providing a JSON response.
     *
     * @return ResultInterface JSON response containing eligibility status and message.
     */
    public function execute(): ResultInterface
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $ruleId = (int)$this->getRequest()->getParam('rule_id');
        $customerId = (int)$this->getRequest()->getParam('customer_id');
        $storeId = (int)$this->getRequest()->getParam('store_id');

        try {
            $eligibility = $this->ruleEligibilityCheck->checkRuleEligibility($customerId, $ruleId, $storeId);

            $result->setData([
                RuleEligibilityCheckInterface::IS_CUSTOMER_ELIGIBLE => $eligibility['isCustomerEligible'],
                'message' => $eligibility[RuleEligibilityCheckInterface::IS_CUSTOMER_ELIGIBLE] ?
                    __('Oohah. The customer can benefit of this rule!') :
                    __($eligibility[RuleEligibilityCheckInterface::VALIDATION_ERROR_MESSAGE])
            ]);
        } catch (NoSuchEntityException $ex) {
            $result->setData([
                RuleEligibilityCheckInterface::IS_CUSTOMER_ELIGIBLE => false,
                'message' => __('Make sure the rule and the customer exist and try again.')
            ]);
        } catch (LocalizedException|\Exception $ex) {
            $result->setData([
                RuleEligibilityCheckInterface::IS_CUSTOMER_ELIGIBLE => false,
                'message' => __('Something went wrong, please try again later.')
            ]);
        }

        return $result;
    }
}
