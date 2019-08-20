<?php

namespace App\Console\Commands\Lastfm\Tag;

use App\Jobs\Lastfm\Tag\ProcessTagInfo;
use Illuminate\Console\Command;

class TagInfoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lastfm:tag:info {tag}';

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
        ProcessTagInfo::dispatch($this->argument('tag'));
    }
}
