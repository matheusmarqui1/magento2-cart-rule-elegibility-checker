<?php
declare(strict_types=1);

namespace Ytec\RuleEligibilityCheck\Model\Processor;

use Magento\Framework\Phrase;
use Ytec\RuleEligibilityCheck\Api\ValidationStepProcessorInterface;
use Ytec\RuleEligibilityCheck\Model\HumanReadableRuleFormatter;

/**
 * Class RuleConditionsValidatorProcessor
 *
 * @package Ytec\RuleEligibilityCheck\Model\Processor
 */
class RuleConditionsValidatorProcessor implements ValidationStepProcessorInterface
{
    const THE_CART_DOES_NOT_MEET_THE_CONDITIONS = 'The cart does not meet the conditions of rule %1: %2.';
    private ?Phrase $failedMessage = null;

    private HumanReadableRuleFormatter $humanReadableRuleFormatter;

    public function __construct(HumanReadableRuleFormatter $humanReadableRuleFormatter)
    {
        $this->humanReadableRuleFormatter = $humanReadableRuleFormatter;
    }

    /**
     * Validates the cart against the conditions of the given Sales Rule.
     *
     * The method checks whether the shopping cart satisfies the conditions
     * specified in the Sales Rule. If the conditions are not met, it will return false.
     *
     * @param \Magento\SalesRule\Model\Rule $rule Sales rule object to validate.
     * @param \Magento\Quote\Api\Data\CartInterface $cart Shopping cart object to validate against the rule.
     *
     * @return bool True if the cart satisfies the rule conditions, false otherwise.
     *
     * @inheritDoc
     */
    public function execute(\Magento\SalesRule\Model\Rule $rule, \Magento\Quote\Api\Data\CartInterface $cart): bool
    {
        /** @var \Magento\Quote\Api\Data\CartInterface|\Magento\Framework\DataObject $cart */
        if ($rule->validate($cart)) {
            return true;
        }

        $this->setFailedMessageParams($rule);
        return false;
    }

    private function setFailedMessageParams(\Magento\SalesRule\Model\Rule $rule): void
    {
        $this->failedMessage = __(
            self::THE_CART_DOES_NOT_MEET_THE_CONDITIONS,
            $rule->getName(),
            $this->humanReadableRuleFormatter->formatConditions($rule)
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
