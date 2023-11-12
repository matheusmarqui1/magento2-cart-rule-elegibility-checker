<?php
declare(strict_types=1);

namespace Ytec\RuleEligibilityCheck\Api;

/**
 * Interface RuleEligibilityCheckInterface
 *
 * Provides methods to check if a customer is eligible for cart rules
 *
 * @package Ytec\RuleEligibilityCheck\Api
 * @api
 */
interface RuleEligibilityCheckInterface
{
    /**
     * Json/Array key for the eligibility result.
     */
    public const IS_CUSTOMER_ELIGIBLE = 'isCustomerEligible';

    /**
     * Json/Array key for the validation error message.
     */
    public const VALIDATION_ERROR_MESSAGE = 'validationErrorMessage';

    /**
     * Check if a customer is eligible for a given cart rule.
     *
     * @param int $customerId Customer ID
     * @param int $ruleId Cart rule ID
     * @param int $storeId Store ID
     * @return array with a boolean value to indicate if the customer is eligible, and a validation message if applicable.
     * @throws \Magento\Framework\Exception\NoSuchEntityException If customer, rule or store does not exist
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkRuleEligibility(int $customerId, int $ruleId, int $storeId): array;
}
