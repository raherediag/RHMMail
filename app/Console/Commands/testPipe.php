<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class testPipe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:for {name} {from} {to} {--queue= : Whether the job should be queued}';

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
        $data = $this->argument();
        set_time_limit(0);
//        $bar = $this->output->createProgressBar(($data['to'] - $data['from']) + 1);
        
        $this->info($data['name']);
        $data['status'] = 'processing';
        $data['mypid'] = getmypid();
        $data['memory'] = memory_get_usage();
        $data['step'] = 0;
        $data['total'] = ($data['to'] - $data['from']) + 1;
        
        $this->createProcess($data);
//        $bar->setFormat(' %current%/%max% [%bar%] %percent:1s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
        
        for($i = $data['from']; $i <= $data['to']; $i++){
            sleep(1);
            \Storage::append($data['name'].'.txt', $i . ' ');
            $data['step'] += 1;
            $data['memory'] = memory_get_usage();
            $this->updateProcess($data);
//            $this->info(json_encode($data));
//            $bar->advance();
        }
        $data['status'] = 'Done';
        $this->updateProcess($data);
//        $bar->finish();

    }
    
    private function createProcess($data){
        if(\Cache::has('processes')){
            $processes = json_decode(\Cache::get('processes'),true);
            $processes[$data['name']] = $data;
            \Cache::put('processes',json_encode($processes),(60*24*7));
        }else{
            \Cache::add('processes',json_encode([$data['name'] => $data]),(60*24*7));
        }
    }
    
    private function updateProcess($data){
        if(\Cache::has('processes')){
            $processes = json_decode(\Cache::get('processes'),true);
            $this->info(json_encode($processes));
            if(isset($processes[$data['name']])){                
                $processes[$data['name']] = $data;                
                \Cache::put('processes',json_encode($processes),(60*24*7));
            }
        }
    }
}
