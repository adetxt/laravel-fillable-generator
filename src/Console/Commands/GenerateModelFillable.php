<?php

namespace Adetxt\LaravelFillableGenerator\Console\Commands;

use Adetxt\LaravelFillableGenerator\Printer\DefaultPrinter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Nette\PhpGenerator\PhpFile;
use SplFileInfo;

class GenerateModelFillable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate-model-fillable
                            {names?*}
                            {--P|path : Model path (default: app\Models)}
                            {--E|excludecol=id,created_at,updated_at,deleted_at : Exclude column}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate model fillable from database table';

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
     * @return int
     */
    public function handle()
    {
        $names = $this->argument('names');
        $error = 0;
        $success = 0;

        $excludecol = explode(',', $this->option('excludecol'));

        $path = $this->option('path');
        $path = empty($path) ? app_path('Models') : $path;

        $this->line('Starting to sync model fillable...');

        if (empty($names)) {
            $this->info('No table name given. All tables will be processed.');
            if (!$this->confirm('Do you wish to continue?')) {
                return 1;
            }
        }

        $names = empty($names) ? File::files($path) : $names;

        if ($names[0] instanceof SplFileInfo) {
            $names = collect($names)->map(function (SplFileInfo $i) {
                return str_replace('.php', '', $i->getFilename());
            })->toArray();
        }

        foreach ($names as $name) {
            try {
                $this->syncModelFillable($name, $path, $excludecol);

                $this->info($name . ' generated');
                $success++;
            } catch (\Throwable $th) {
                $this->error($name . ' failed to sync because ' . $th->getMessage());
                $error++;
            }
        }

        $this->comment(sprintf('Model fillable has been generated with : %d success, %d error', $success, $error));

        return 0;
    }

    public function syncModelFillable($model_name, $path, $excludecol)
    {
        $fileName = $path . '/' . $model_name . '.php';

        $class = PhpFile::fromCode(file_get_contents($fileName));

        $ns = collect($class->getNamespaces())->first()->getName();
        $model = $ns . '\\' . $model_name;

        $columns = DB::getSchemaBuilder()->getColumnListing((new $model)->getTable());
        $fillable = collect($columns)->filter(fn ($i) => !in_array($i, $excludecol))->values()->toArray();

        $hasFillableProperties = $class->getClasses()[$model]->getProperties()['fillable'] ?? false;

        if (!$hasFillableProperties) {
            $class->getClasses()[$model]->addProperty('fillable', $fillable)->setProtected();
        } else {
            $class->getClasses()[$model]
                ->getProperties()['fillable']
                ->setValue($fillable);
        }

        $printer = new DefaultPrinter;

        file_put_contents($fileName, $printer->printFile($class));

        return true;
    }
}
