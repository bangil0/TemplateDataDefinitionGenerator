<?php namespace Sule\Tdd\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class TddGeneratorCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:tdd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Template Data Definition.';

    /**
     * Model generator instance.
     *
     * @var Sule\Tdd\Generators\ModelGenerator
     */
    protected $generator;

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
     * @return void
     */
    public function fire()
    {
        $path = $this->getPath();
        $template = $this->option('template');

        $this->printResult($this->generator->make($path, $template), $path);
    }

    /**
     * Provide user feedback, based on success or not.
     *
     * @param  boolean $successful
     * @param  string $path
     * @return void
     */
    protected function printResult($successful, $path)
    {
        if ($successful) {
            return $this->info("Created {$path}");
        }

        $this->error("Could not create {$path}");
    }

    /**
     * Get the path to the file that should be generated.
     *
     * @return string
     */
    protected function getPath()
    {
        return $this->option('path') . '/' . ucwords($this->argument('name')) . '.php';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('path', null, InputOption::VALUE_OPTIONAL, 'Path to the models directory.', app_path() . '/models'),
            array('template', null, InputOption::VALUE_OPTIONAL, 'Path to template.', __DIR__.'/../Generators/templates/model.txt')
        );
    }

}
