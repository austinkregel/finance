<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\Repositories\CategoryRepository;
use App\Contracts\Services\PlaidServiceContract;
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Psr\Log\LoggerInterface;

class SyncCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plaid:sync-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all plaid categories';

    /**
     * @var PlaidServiceContract
     */
    protected $plaidService;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * Synccategories constructor.
     * @param PlaidServiceContract $plaidService
     * @param LoggerInterface $logger
     */
    public function __construct(PlaidServiceContract $plaidService, LoggerInterface $logger)
    {
        parent::__construct();
        $this->plaidService = $plaidService;
        $this->logger = $logger;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle($page = 1)
    {
        $this->info('Attempting to sync the categories from the plaid services... One moment please...');
        $categories = new Collection($this->plaidService->getCategories());

        $bar = $this->output->createProgressBar(count($categories));

        $bar->setRedrawFrequency(10);

        $categories = $categories->map(function ($category) {
            $class = new \stdClass();

            $class->category_id = $category->category_id;
            $class->name = array_last($category->hierarchy);

            return $class;
        });

        $categories_id = $categories->map->category_id;

        // Only grab categories that are in this subset of data to avoid memory bloat
        $categoriesFromTheDatabase = Category::whereIn('category_id', $categories_id->toArray())->get();

        /** @var Collection $categoryIds */
        $categoryIds = $categoriesFromTheDatabase->map->category_id;

        foreach ($categories as $category) {
            $bar->advance();
            if ($categoryIds->contains($category->category_id)) {
                // If the value already exists in our database we don't want to create it again.
                continue;
            }

            Category::create([
                'name' => $category->name,
                'category_id' => $category->category_id,
            ]);
        }

        $bar->finish();
    }
}
