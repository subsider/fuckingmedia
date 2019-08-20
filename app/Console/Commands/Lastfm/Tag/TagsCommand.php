<?php

namespace App\Console\Commands\Lastfm\Tag;

use App\Jobs\Lastfm\Tag\ProcessTags;
use Illuminate\Console\Command;

class TagsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lastfm:tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ProcessTags::dispatch();
    }
}
