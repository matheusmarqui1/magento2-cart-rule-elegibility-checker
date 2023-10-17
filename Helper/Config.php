<?php

namespace Ytec\RuleEligibilityCheck\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * Class Config
 *
 * Helper class for managing the Rule Eligibility Checker module configuration.
 *
 * @package Ytec\RuleEligibilityCheck\Helper
 */
class Config extends AbstractHelper
{
    /**
     * XML path to the configuration option for enabling/disabling the module.
     */
    public const IS_MODULE_ENABLED_XML_PATH = 'ytec_rule_eligibility_checker/general/enabled';

    /**
     * Config constructor.
     *
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    /**
     * Checks if the Rule Eligibility Checker module is enabled.
     *
     * @return bool True if the module is enabled, false otherwise.
     */
    public function isModuleEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::IS_MODULE_ENABLED_XML_PATH);
    }
}
