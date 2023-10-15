<?php
declare(strict_types=1);

namespace Ytec\RuleEligibilityCheck\Model\Processor;

use Magento\Framework\Phrase;
use Ytec\RuleEligibilityCheck\Api\ValidationStepProcessorInterface;

/**
 * Class DateValidationProcessor
 *
 * @package Ytec\RuleEligibilityCheck\Model\Processor
 */
class DateValidationProcessor implements ValidationStepProcessorInterface
{
    const THE_RULE_IS_ALREADY_EXPIRED = 'The rule \'%1\' is already expired, it ended %2.';
    private ?Phrase $failedMessage = null;

    /**
     * Executes the date validation for a given Sales Rule and Cart.
     *
     * This processor checks if the sales rule is still applicable based on the
     * "to date" configured in the rule settings. It returns false if the rule
     * has expired, and true otherwise.
     *
     * @param \Magento\SalesRule\Model\Rule $rule Sales rule object.
     * @param \Magento\Quote\Api\Data\CartInterface $cart Shopping cart object.
     *
     * @return bool True if the sales rule is still applicable, false otherwise.
     */
    public function execute(\Magento\SalesRule\Model\Rule $rule, \Magento\Quote\Api\Data\CartInterface $cart): bool
    {
        $currentDate = strtotime(date('Y-m-d'));

        if ($rule->getToDate() && strtotime($rule->getToDate()) < $currentDate) {
            $this->setFailedMessageParams($rule->getName(), $rule->getToDate());
            return false;
        }

        return true;
    }

    private function setFailedMessageParams(string ...$params): void
    {
        $this->failedMessage = __(self::THE_RULE_IS_ALREADY_EXPIRED, ...$params);
    }

    /**
     * @inheritDoc
     */
    public function getFailedMessage(): Phrase
    {
        return $this->failedMessage;
    }
}
