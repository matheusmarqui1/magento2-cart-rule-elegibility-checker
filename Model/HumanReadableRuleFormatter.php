<?php

namespace Ytec\RuleEligibilityCheck\Model;

use Magento\SalesRule\Model\Rule;

/**
 * Class HumanReadableRuleFormatter
 * @package Ytec\RuleEligibilityCheck\Model
 */
class HumanReadableRuleFormatter
{
    /**
     * Mapping operators to human-friendly terms
     */
    private array $operatorMap = [
        '==' => 'is equal to',
        '!=' => 'is not equal to',
        '>=' => 'is greater than or equal to',
        '<=' => 'is less than or equal to'
    ];

    /**
     * Formats rule conditions to a human-readable string.
     *
     * @param Rule $rule
     * @param string $by format conditions by actions/conditions
     * @param array $failedRules
     * @return string
     */
    public function formatConditions(
        \Magento\SalesRule\Model\Rule $rule,
        string $by = 'conditions',
        array $failedRules = []
    ): string {
        if ($by === 'actions') {
            $conditions = $rule->getActions()->asArray();
        } else {
            $conditions = $rule->getConditions()->asArray();
        }

        return $this->parseConditions($conditions['conditions'], $conditions['aggregator']);
    }

    /**
     * Recursive function to parse conditions array.
     *
     * @param array $conditions
     * @param string $aggregator
     * @return string
     */
    private function parseConditions(array $conditions, string $aggregator): string
    {
        $descriptions = [];

        foreach ($conditions as $condition) {
            if (isset($condition['conditions'])) {
                $nestedDescription = $this->parseConditions($condition['conditions'], $condition['aggregator']);
                $descriptions[] = '(' . $nestedDescription . ')';
            } else {
                $attribute = ucwords(str_replace('_', ' ', $condition['attribute']));
                $operator = $this->operatorMap[$condition['operator']] ?? $condition['operator'];
                $value = $condition['value'];

                $descriptions[] = "{$attribute} {$operator} {$value}";
            }
        }

        return implode(sprintf(' %s ', $aggregator === 'all' ? 'and' : 'or'), $descriptions);
    }
}
