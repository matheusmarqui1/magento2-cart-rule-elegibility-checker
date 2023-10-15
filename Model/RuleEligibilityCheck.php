<?php
declare(strict_types=1);

namespace Ytec\RuleEligibilityCheck\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\SalesRule\Model\Data\Rule as RuleData;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\SalesRule\Model\Rule;
use Psr\Log\LoggerInterface;
use Ytec\RuleEligibilityCheck\Api\RuleEligibilityCheckInterface;
use Ytec\RuleEligibilityCheck\Api\ValidationStepProcessorInterface;

/**
 * Class RuleEligibilityCheck
 *
 * @package Ytec\RuleEligibilityCheck\Model
 */
class RuleEligibilityCheck implements RuleEligibilityCheckInterface
{
    private CartRepositoryInterface $cartRepository;
    private RuleCollectionFactory $ruleCollectionFactory;
    private LoggerInterface $logger;

    /**
     * @var ValidationStepProcessorInterface[]
     */
    private array $processors;

    private array $response = [
        'isCustomerEligible' => true,
        'validationErrorMessage' => null
    ];

    /**
     * RuleEligibilityCheck constructor.
     *
     * @param CartRepositoryInterface $cartRepository
     * @param RuleCollectionFactory $ruleCollectionFactory
     * @param LoggerInterface $logger
     * @param array $processors
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        RuleCollectionFactory $ruleCollectionFactory,
        LoggerInterface $logger,
        array $processors = []
    ) {
        $this->cartRepository = $cartRepository;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->processors = $processors;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function checkRuleEligibility(int $customerId, int $ruleId): array
    {
        /** @var CartInterface|AbstractModel $cart */
        $cart = $this->cartRepository->getActiveForCustomer($customerId);

        /** @var Rule $rule */
        $rule = $this->ruleCollectionFactory->create()
            ->addFieldToFilter(RuleData::KEY_RULE_ID, $ruleId)
            ->getFirstItem();

        foreach ($this->processors as $processor) {
            $isProcessorValidated = $processor->execute($rule, $cart);

            $this->logger->debug('ruleDebugger', [
                'processorClass' => get_class($processor),
                'result' => $isProcessorValidated ? 'Validated' : 'Not Validated',
                'actions' => $rule->getActions()->asArray()
            ]);

            if (!$isProcessorValidated) {
                $this->response['isCustomerEligible'] = false;
                $this->response['validationErrorMessage'] = $processor->getFailedMessage();
                return $this->response;
            }
        }

        return $this->response;
    }
}
