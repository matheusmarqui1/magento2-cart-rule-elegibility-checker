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
     * Check if a customer is eligible for a given cart rule.
     *
     * @param int $customerId Customer ID
     * @param int $ruleId Cart rule ID
     * @return array with a boolean value to indicate if the customer is eligible, and a validation message if applicable.
     * @throws \Magento\Framework\Exception\NoSuchEntityException If customer or rule does not exist
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkRuleEligibility(int $customerId, int $ruleId): array;
}
