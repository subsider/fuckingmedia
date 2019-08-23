<?php

namespace App\Jobs\Discogs\Label;

use App\Http\Clients\Discogs\DiscogsClient;
use App\Repositories\Discogs\LabelRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessLabelInfo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private $id;

    /**
     * Create a new job instance.
     *
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @param DiscogsClient $client
     * @param LabelRepository $labelRepository
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(DiscogsClient $client, LabelRepository $labelRepository)
    {
        $result = $client->label()
            ->info($this->id)
            ->get();

        $result['title'] = $result['name'];
        $label = $labelRepository->create($result);
        $labelRepository->addService($label, $result);
    }
}
