<?php

use Illuminate\Database\Eloquent\Builder;
use JWadhams\JsonLogic;

if (!function_exists('queryTransformer')) {

    /**
     * description
     *
     * @param array $rules
     * @param Builder $query
     * @param string $combinator
     * @return Builder
     */
    function queryTransformer($rules, $query, $combinator = 'and')
    {
        foreach ($rules as $rule) {
            if (isset($rule['rules'])) {
                // Nested rule, use a closure to handle recursion
                $query->where(function($nestedQuery) use ($rule) {
                    queryTransformer($rule['rules'], $nestedQuery, $rule['combinator']);
                }, null, null, $rule['not'] ? 'not' : $combinator);
            } else {
                // Single rule, map operators to query builder methods
                $field = $rule['field'];
                $value = $rule['value'];
                $operator = $rule['operator'];

                switch ($operator) {
                    case '=':
                        $query->where($field, '=', $value);
                        break;
                    case '!=':
                        $query->where($field, '!=', $value);
                        break;
                    case '<':
                        $query->where($field, '<', $value);
                        break;
                    case '>':
                        $query->where($field, '>', $value);
                        break;
                    case '<=':
                        $query->where($field, '<=', $value);
                        break;
                    case '>=':
                        $query->where($field, '>=', $value);
                        break;
                    case 'contains':
                        $query->where($field, 'LIKE', '%' . $value . '%');
                        break;
                    case 'beginsWith':
                        $query->where($field, 'LIKE', $value . '%');
                        break;
                    case 'endsWith':
                        $query->where($field, 'LIKE', '%' . $value);
                        break;
                    case 'doesNotContain':
                        $query->where($field, 'NOT LIKE', '%' . $value . '%');
                        break;
                    case 'doesNotBeginWith':
                        $query->where($field, 'NOT LIKE', $value . '%');
                        break;
                    case 'doesNotEndWith':
                        $query->where($field, 'NOT LIKE', '%' . $value);
                        break;
                    case 'null':
                        $query->whereNull($field);
                        break;
                    case 'notNull':
                        $query->whereNotNull($field);
                        break;
                    case 'in':
                        $query->whereIn($field, explode(',', $value));
                        break;
                    case 'notIn':
                        $query->whereNotIn($field, explode(',', $value));
                        break;
                    case 'between':
                        $range = explode(',', $value);
                        $query->whereBetween($field, [$range[0], $range[1]]);
                        break;
                    case 'notBetween':
                        $range = explode(',', $value);
                        $query->whereNotBetween($field, [$range[0], $range[1]]);
                        break;
                }
            }
        }

        return $query;
    }
}
