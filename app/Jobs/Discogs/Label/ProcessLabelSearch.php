<?php

namespace App\Jobs\Discogs\Label;

use App\Http\Clients\Discogs\DiscogsClient;
use App\Repositories\Discogs\LabelRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessLabelSearch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var string
     */
    private $labelName;

    /**
     * Create a new job instance.
     *
     * @param string $labelName
     */
    public function __construct(string $labelName)
    {
        $this->labelName = $labelName;
    }

    /**
     * @param DiscogsClient $client
     * @param LabelRepository $labelRepository
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(DiscogsClient $client, LabelRepository $labelRepository)
    {
        $page = 1;

        do {
            $results = $client->artist()
                ->search($this->labelName)
                ->limit(50)
                ->page($page)
                ->get();


            dump("Page {$page}");
            collect($results['results'])->each(function ($result) use ($labelRepository) {
                $label = $labelRepository->create($result);
                $labelRepository->addService($label, $result)
                    ->addImages($label, $result);
            });

            $page++;
        } while ($page <= DiscogsClient::MAX_PAGE);
    }
}
