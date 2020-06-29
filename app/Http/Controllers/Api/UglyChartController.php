<?php


namespace App\Http\Controllers\Api;


use App\Contracts\Repositories\TransactionRepositoryContract;
use App\Filters\Metrics\TrendFilter;
use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use App\Tag;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class UglyChartController
{
    protected TransactionRepositoryContract $transactionRepository;

    protected TrendFilter $trendFilter;

    public function __invoke(Request $request, $type, $model)
    {
        abort_unless($model === 'tag', 400, 'Unsupported model ' .$model);

        $this->transactionRepository = new TransactionRepository;
        $this->trendFilter = new TrendFilter;

        ['current' => $data, 'previous' => $previousData] = $this->fetchDataForModel($model, $request);

        if (empty($data)) {
            abort(400, 'No data');
        }

        return $this->formatType($request, $type, $data, $previousData);
    }

    public function fetchDataForModel($model, $request)
    {
        ['current' => $currentDuration, 'previous' => $previousDuration] = $this->formatDuration($request);

        [$currentDurationStart, $currentDurationEnd] = $currentDuration;
        [$previousDurationStart, $previousDurationEnd] = $previousDuration;

        return [
            'current' => $this->fetchTransactionsForTagBetweenDates($currentDurationStart, $currentDurationEnd, $request),
            'previous' => $this->fetchTransactionsForTagBetweenDates($previousDurationStart, $previousDurationEnd, $request),
        ];
    }

    protected function fetchTransactionsForTagBetweenDates(Carbon $start, Carbon $end, Request $request)
    {
        return $this->transactionRepository->findAllBetweenDateForUserInScope($request->user(), $start, $end, $request->get('scope'));
    }

    public function formatDuration(Request $request): array
    {
        switch ($request->get('duration')) {
            case '14d':
                return [
                    'current' => [now()->subWeeks(2), now()],
                    'previous' => [now()->subWeeks(4), now()->subWeeks(2)]
                ];
            case '1m':
                return [
                    'current' => [now()->subMonth(), now()],
                    'previous' => [now()->subMonths(2), now()->subMonth()]
                ];
            case '1y':
                return [
                    'current' => [now()->subYear(), now()],
                    'previous' => [now()->subYears(2), now()->subYear()]
                ];
            case 'mtd':
                return [
                    'current' => [now()->startOfMonth(), now()],
                    'previous' => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]
                ];
            case 'ytd':
                return [
                    'current' => [now()->startOfYear(), now()],
                    'previous' => [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()]
                ];
            case '7d':
            default:
                return [
                    'current' => [now()->subWeek(), now()],
                    'previous' => [now()->subWeeks(2), now()->subWeek()]
                ];
        }
    }

    public function formatType($request, $type, Collection $data, Collection $previousData)
    {
        ['current' => $currentDuration, 'previous' => $previousDuration] = $this->formatDuration($request);

        $returnedData = [];

        switch ($type) {
            // Trends are line graphs. They need keys to be the dates, and the values to be the total.
            case 'trend':
                $returnedData = [
                    'current' => $this->trendFilter->handle($currentDuration, $data),
                    'previous' => $this->trendFilter->handle($previousDuration, $previousData),
                ];

                break;
            // Values are displayed literally. So whatever is returned is dispalyed.
            case 'value':
                $returnedData = [
                    'current' => $data->sum('amount'),
                    'previous' => $previousData->sum('amount'),
                ];
                break;
        }

        return $returnedData;
    }
}
