<?php

namespace App\Console\Commands;

use App\Contracts\Services\PlaidServiceContract;
use App\Models\Institution;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;

class SyncInstitutions extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plaid:sync-institutions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all plaid institutions';

    /**
     * @var PlaidServiceContract
     */
    protected $plaidService;

    /**
     * @var LoggerInterface
     */
    protected $logger;

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
    public function handle($page = 1, $pageSize = 500)
    {
        $this->info('Attempting to sync the institutions from the plaid services... Institutions that have already been synced will not be re-created.');

        do {
            $categories = $this->plaidService->getInstitutions($pageSize, $page++);

            if (empty($bar)) {
                $bar = $this->output->createProgressBar($categories->total());
                $bar->setRedrawFrequency(10);
            }

            foreach ($categories->items() as $item) {
                if (!Institution::where('institution_id', $item->institution_id)->exists()) {
                    if ($item->logo) {
                        $logo = 'institutions/' . $item->institution_id . '.png';
                        file_put_contents(public_path($logo), base64_decode($item->logo));
                    }

                    Institution::create([
                        'name' => $item->name,
                        'institution_id' => $item->institution_id,
                        'logo' => $logo ?? null,
                        'products' => $item->products,
                        'site_url' => $item->url ?? null,
                        'primary_color' => $item->primary_color ?? null,
                    ]);
                }
                $bar->advance();
            }
        } while ($categories->hasMorePages());
        $bar->finish();
    }
}
