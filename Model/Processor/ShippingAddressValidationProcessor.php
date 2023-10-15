<?php
declare(strict_types=1);

namespace Ytec\RuleEligibilityCheck\Model\Processor;

use Magento\Framework\Phrase;
use Magento\SalesRule\Model\Utility as SalesRuleUtility;
use Ytec\RuleEligibilityCheck\Model\HumanReadableRuleFormatter;

/**
 * Class ShippingAddressValidationProcessor
 *
 * Validates shipping address against the given Sales Rule.
 *
 * @package Ytec\RuleEligibilityCheck\Model\Processor
 */
class ShippingAddressValidationProcessor implements \Ytec\RuleEligibilityCheck\Api\ValidationStepProcessorInterface
{
    const THE_RULE_CANNOT_BE_APPLIED = 'The rule \'%1\' cannot be applied due to shipping address, customer or cart restrictions: %2.';
    private ?Phrase $failedMessage = null;

    /**
     * @var SalesRuleUtility
     */
    private SalesRuleUtility $salesRuleUtility;

    /**
     * @var HumanReadableRuleFormatter
     */
    private HumanReadableRuleFormatter $humanReadableRuleFormatter;

    /**
     * ShippingAddressValidationProcessor constructor.
     *
     * @param SalesRuleUtility $salesRuleUtility
     * @param HumanReadableRuleFormatter $humanReadableRuleFormatter
     */
    public function __construct(
        SalesRuleUtility $salesRuleUtility,
        HumanReadableRuleFormatter $humanReadableRuleFormatter
    ) {
        $this->salesRuleUtility = $salesRuleUtility;
        $this->humanReadableRuleFormatter = $humanReadableRuleFormatter;
    }

    /**
     * Validates the shipping address against the given Sales Rule.
     *
     * This method will use the Magento SalesRule utility to evaluate the shipping
     * address associated with the given cart against the conditions specified in
     * the provided Sales Rule.
     *
     * @param \Magento\SalesRule\Model\Rule $rule Sales rule to validate.
     * @param \Magento\Quote\Api\Data\CartInterface $cart Cart with the shipping address.
     *
     * @return bool True if the shipping address satisfies the rule, false otherwise.
     *
     * @inheritDoc
     */
    public function execute(\Magento\SalesRule\Model\Rule $rule, \Magento\Quote\Api\Data\CartInterface $cart): bool
    {
        if (!$this->hasShippingConditions($rule)) {
            /** If there's no shipping conditions we consider the shipping validation as ok. */
            return true;
        }

        if (!$this->salesRuleUtility->canProcessRule($rule, $cart->getShippingAddress())) {
            $this->setFailedMessageParams($rule->getName(), $this->humanReadableRuleFormatter->formatConditions($rule));
            return false;
        }

        return true;
    }

    /**
     * Checks if the given Sales Rule has any shipping conditions.
     *
     * @param \Magento\SalesRule\Model\Rule $rule $rule
     * @return bool
     */
    public function hasShippingConditions(\Magento\SalesRule\Model\Rule $rule): bool
    {
        $conditions = $rule->getConditions()->asArray();

        return $this->searchForShippingConditions($conditions);
    }

    /**
     * Recursive function to search for shipping conditions in the conditions array.
     *
     * @param array $conditions
     * @return bool
     * @noinspection PhpStrFunctionsInspection
     */
    private function searchForShippingConditions(array $conditions): bool
    {
        /** Using str_pos instead of str_contains for php 7.4 compatibility. */
        if (isset($conditions['attribute']) && strpos($conditions['attribute'], 'shipping_') !== false) {
            return true;
        }

        if (isset($conditions['conditions'])) {
            foreach ($conditions['conditions'] as $condition) {
                if ($this->searchForShippingConditions($condition)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function setFailedMessageParams(string ...$params): void
    {
        $this->failedMessage = __(
            self::THE_RULE_CANNOT_BE_APPLIED,
            ...$params
        );
    }

    /**
     * @inheritDoc
     */
    public function getFailedMessage(): Phrase
    {
        return $this->failedMessage;
    }
}
