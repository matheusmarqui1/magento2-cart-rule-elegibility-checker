<?php
declare(strict_types=1);

namespace Ytec\RuleEligibilityCheck\Api;

use Magento\Framework\Phrase;

/**
 * Interface ValidationStepProcessorInterface
 *
 * @package Ytec\RuleEligibilityCheck\Api
 */
interface ValidationStepProcessorInterface
{
    /**
     * Executes the validation process based on the provided Rule and Cart.
     *
     * @param \Magento\SalesRule\Model\Rule $rule Sales rule object.
     * @param \Magento\Quote\Api\Data\CartInterface $cart Shopping cart object.
     *
     * @return bool True if the validation is successful, false otherwise.
     */
    public function execute(\Magento\SalesRule\Model\Rule $rule, \Magento\Quote\Api\Data\CartInterface $cart): bool;

    /**
     * Get the message for a failed validation
     *
     * @return Phrase
     */
    public function getFailedMessage(): \Magento\Framework\Phrase;
}
