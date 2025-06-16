<?php

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

if (! function_exists('isQueryBuilderInRequest')) {
    /**
     * description
     */
    function isQueryBuilderInRequest(Request $request): bool
    {
        return $request->hasAny(['query', 'sort', 'paginate']);
    }
}

if (! function_exists('modelBuilder')) {

    /**
     * description
     */
    function modelBuilder(Request $request, Builder $builder): LengthAwarePaginator
    {
        return paginationTransformer($request, queryBuilder($request, $builder)->toRe);
    }
}

if (! function_exists('queryBuilder')) {

    /**
     * description
     */
    function queryBuilder(Request $request, Builder $builder): Builder
    {
        if ($request->has('query')) {
            $query = json_decode($request->query('query'), true);
            $builder = queryTransformer($query['rules'], $builder, $query['combinator']);
        }

        if ($request->has('sort')) {
            $sort = json_decode($request->query('sort'), true);
            $builder = sortTransformer($sort, $builder);
        }

        return $builder;
    }
}

if (! function_exists('queryTransformer')) {

    /**
     * description
     *
     * @param  array  $rules
     * @param  Builder  $query
     * @param  string  $combinator
     * @return Builder
     */
    function queryTransformer($rules, $query, $combinator = 'and')
    {
        foreach ($rules as $rule) {
            if (isset($rule['rules'])) {
                // Nested rule, use a closure to handle recursion
                $query->where(function ($nestedQuery) use ($rule) {
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
                        $query->where($field, 'LIKE', '%'.$value.'%');
                        break;
                    case 'beginsWith':
                        $query->where($field, 'LIKE', $value.'%');
                        break;
                    case 'endsWith':
                        $query->where($field, 'LIKE', '%'.$value);
                        break;
                    case 'doesNotContain':
                        $query->where($field, 'NOT LIKE', '%'.$value.'%');
                        break;
                    case 'doesNotBeginWith':
                        $query->where($field, 'NOT LIKE', $value.'%');
                        break;
                    case 'doesNotEndWith':
                        $query->where($field, 'NOT LIKE', '%'.$value);
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

if (! function_exists('sortTransformer')) {

    /**
     * description
     */
    function sortTransformer(array $sorts, Builder $query): Builder
    {
        foreach ($sorts as $sort) {
            $query->orderBy($sort['id'], $sort['desc'] ? 'desc' : 'asc');
        }

        return $query;
    }
}

if (! function_exists('paginationTransformer')) {

    /**
     * description
     */
    function paginationTransformer(Request $request, Builder $query): LengthAwarePaginator
    {
        $paginate = ['size' => 50, 'page' => 1];

        if ($request->has('paginate')) {
            $paginate = json_decode($request->query('paginate'), true);
        }

        return $query->paginate($paginate['size'], ['*'], 'page', $paginate['page']);
    }
}
