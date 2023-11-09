<?php

namespace Ytec\RuleEligibilityCheck\Plugin\SalesRule\Model\Rule;

use Magento\SalesRule\Model\Rule\DataProvider as MagentoDataProvider;
use Ytec\RuleEligibilityCheck\Helper\Config as ModuleConfig;

class DataProviderPlugin
{
    private ModuleConfig $config;

    public function __construct(ModuleConfig $config)
    {
        $this->config = $config;
    }

    public function afterGetData(MagentoDataProvider $subject, ?array $result): ?array
    {
        if (is_null($result)) {
            return null;
        }

        foreach ($result as &$data) {
            $data['rule_eligibility_checker_enabled'] = $this->config->isModuleEnabled();
        }

        return $result;
    }
}
