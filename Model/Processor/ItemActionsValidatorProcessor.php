<?php
declare(strict_types=1);

namespace Ytec\RuleEligibilityCheck\Model\Processor;

use Magento\Framework\Phrase;
use Magento\SalesRule\Model\Rule\Action\Discount\CalculatorFactory;
use Ytec\RuleEligibilityCheck\Api\ValidationStepProcessorInterface;
use Ytec\RuleEligibilityCheck\Model\HumanReadableRuleFormatter;

/**
 * Class ItemActionsValidatorProcessor
 *
 * @package Ytec\RuleEligibilityCheck\Model\Processor
 */
class ItemActionsValidatorProcessor implements ValidationStepProcessorInterface
{
    public const NO_ITEMS_IN_THE_CART_MEET_THE_RULE = 'No items in the cart meet the rule\'s action criteria: %1.';
    private ?Phrase $failedMessage = null;

    /**
     * @var CalculatorFactory
     */
    private CalculatorFactory $calculatorFactory;
    private HumanReadableRuleFormatter $humanReadableRuleFormatter;

    /**
     * ItemActionsValidatorProcessor constructor.
     *
     * @param CalculatorFactory $calculatorFactory
     * @param HumanReadableRuleFormatter $humanReadableRuleFormatter
     */
    public function __construct(
        CalculatorFactory $calculatorFactory,
        HumanReadableRuleFormatter $humanReadableRuleFormatter
    ) {
        $this->calculatorFactory = $calculatorFactory;
        $this->humanReadableRuleFormatter = $humanReadableRuleFormatter;
    }

    /**
     * Executes the item actions validation for a given Sales Rule and Cart.
     *
     * This processor iterates through each item in the cart, validates it against
     * the sales rule's actions.
     *
     * @param \Magento\SalesRule\Model\Rule $rule Sales rule object.
     * @param \Magento\Quote\Api\Data\CartInterface $cart Shopping cart object.
     *
     * @return bool True if at least one item in the cart is valid based on the rule's actions, false otherwise.
     *
     * @noinspection PhpUndefinedMethodInspection
     */
    public function execute(\Magento\SalesRule\Model\Rule $rule, \Magento\Quote\Api\Data\CartInterface $cart): bool
    {
        foreach ($cart->getItems() as $item) {
            if ($rule->getActions()->validate($item)) {
                /** @TODO Also validate the discount amount using the calculator. */
                return true;
            }
        }

        $this->setFailedMessageParams($this->humanReadableRuleFormatter->formatConditions($rule, 'actions'));
        return false;
    }

    private function setFailedMessageParams(string ...$params): void
    {
        $this->failedMessage = __(self::NO_ITEMS_IN_THE_CART_MEET_THE_RULE, ...$params);
    }

    /**
     * @inheritDoc
     */
    public function getFailedMessage(): Phrase
    {
        return $this->failedMessage;
    }
}
