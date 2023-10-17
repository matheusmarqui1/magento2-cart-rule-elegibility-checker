<?php
declare(strict_types=1);

namespace Ytec\RuleEligibilityCheck\Model\Processor;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Store\Model\StoreManagerInterface;
use Ytec\RuleEligibilityCheck\Api\ValidationStepProcessorInterface;

/**
 * Class EmptyCartValidationProcessor
 *
 * @package Ytec\RuleEligibilityCheck\Model\Processor
 */
class EmptyCartValidationProcessor implements ValidationStepProcessorInterface
{
    const THE_CART_FOR_CUSTOMER_IS_EMPTY = 'The cart of customer %1 on store %2 is empty.';
    private ?Phrase $failedMessage = null;
    private StoreManagerInterface $storeManager;

    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * Validates if the cart is empty.
     *
     * @param \Magento\SalesRule\Model\Rule $rule Sales rule object to validate.
     * @param \Magento\Quote\Api\Data\CartInterface $cart Shopping cart object to validate against the rule.
     *
     * @return bool True if the cart is not empty, false otherwise.
     *
     * @inheritDoc
     * @throws NoSuchEntityException
     */
    public function execute(\Magento\SalesRule\Model\Rule $rule, \Magento\Quote\Api\Data\CartInterface $cart): bool
    {
        if ($cart->getItemsCount() > 0) {
            return true;
        }

        $this->setFailedMessageParams($cart);
        return false;
    }

    /**
     * @throws NoSuchEntityException
     */
    private function setFailedMessageParams(\Magento\Quote\Api\Data\CartInterface $cart): void
    {
        $this->failedMessage = __(
            self::THE_CART_FOR_CUSTOMER_IS_EMPTY,
            $cart->getCustomer()->getFirstname(),
            $this->storeManager->getStore($cart->getStoreId())->getCode()
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
