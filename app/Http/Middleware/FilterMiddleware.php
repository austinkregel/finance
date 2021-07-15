<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FilterMiddleware
{
    protected $wheres = [];

    public function handle(Request $request, Closure $next)
    {
        if ($request->has('filter')) {
            $request = $request->merge([
                'filters' => $this->filterValues($request),
            ]);
        }

        return $next($request);
    }

    protected function filterValues(Request $request)
    {
        $filters = $request->get('filter', []);

        foreach ($filters as $column => $query) {
            $parts = explode(':', $query, 2);

            if (count($parts) !== 2) {
                throw ValidationException::withMessages([
                    'filter' => [
                        sprintf('In your query, you are missing the second part of your filter for %s.', $column),
                    ],
                ]);
            }

            [$operator, $queryString] = $parts;

            $operator = $this->normalizeOperator($operator);

            $valueToSearchFor = $this->normalizeSearchValue($queryString);

            $this->buildWhere($column, $operator, $valueToSearchFor);
        }

        return $this->wheres;
    }

    protected function buildWhere($column, $operator, array $value): void
    {
        if ($operator === 'between') {
            $this->wheres[] = [$column, '>=', $value[0]];
            $this->wheres[] = [$column, '<=', $value[1]];
        } elseif (in_array($operator, ['in', 'not in'])) {
            $this->wheres[] = [$column, $operator, $value];
        } else {
            $this->wheres[] = [$column, $operator, Arr::first($value)];
        }
    }

    protected function normalizeOperator(string $operator): string
    {
        switch ($operator) {
            case 'gt':
            case '>':
                return '>';
            case 'gte':
            case '>=':
                return '>=';
            case 'lt':
            case '<':
                return '<';
            case 'lte':
            case '<=':
                return '<=';
            case 'eq':
            case '=':
                return '=';
            case 'ne':
            case '!=':
            case '<>':
                return '!=';
            case 'in':
                return 'in';
            case 'notin':
                return 'not in';
            case 'like':
                return 'like';
            case 'between':
                return 'between';
        }
    }

    protected function normalizeSearchValue(string $queryString): array
    {
        if (Str::contains($queryString, ['%', '*'])) {
            $queryString = str_replace('*', '%', $queryString);
        }

        $queryValues = array_map('trim', explode(',', $queryString));

        return array_map([$this, 'parseValueToNative'], $queryValues);
    }

    protected function parseValueToNative($value)
    {
        if (is_numeric($value)) {
            if (Str::contains($value, '.')) {
                return (float) $value;
            }

            return (int) $value;
        } elseif (strtolower($value) === 'false') {
            return false;
        } elseif (strtolower($value) === 'true') {
            return true;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return $value;
        }
    }
}
