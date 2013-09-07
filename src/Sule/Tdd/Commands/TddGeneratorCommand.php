<?php
namespace Sule\Tdd\Commands;

use Illuminate\Console\Command;

use Sule\Tdd\Generators\Generator;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class TddGeneratorCommand extends Command
{

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
     * @param  Sule\Tdd\Generators\Generator $generator
     * @return void
     */
    public function __construct(Generator $generator)
    {
        parent::__construct();

        $this->generator = $generator;
    }

    /**
     * Return the generator.
     *
     * @return Sule\Tdd\TddGenerator
     */
    protected function getGenerator()
    {
        return $this->generator;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $path                       = $this->option('path');
        $template                   = $this->option('template');
        $templateInterface          = $this->option('templateInterface');
        $templateMethod             = $this->option('templateMethod');
        $templateMethodInterface    = $this->option('templateMethodInterface');
        $removeSSuffixFromTableName = $this->option('removeSSuffixFromTableName');

        if ($removeSSuffixFromTableName == 'Yes') {
            $removeSSuffixFromTableName = true;
        } else {
            $removeSSuffixFromTableName = false;
        }

        $generator = $this->getGenerator();

        $generator->setTemplates(
            $template, 
            $templateInterface, 
            $templateMethod, 
            $templateMethodInterface
        );

        $this->printResult($generator->make($path, $removeSSuffixFromTableName), $path);

        unset($generator);
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
            array(
                'path', 
                null, 
                InputOption::VALUE_OPTIONAL, 
                'Path to the templates directory.', 
                app_path() . '/templates'
            ),
            array(
                'template', 
                null, 
                InputOption::VALUE_OPTIONAL, 
                'Path to template.', 
                __DIR__.'/../Generators/templates/tdd.txt'
            ),
            array(
                'templateInterface', 
                null, 
                InputOption::VALUE_OPTIONAL, 
                'Path to template interface.', 
                __DIR__.'/../Generators/templates/tddInterface.txt'
            ),
            array(
                'templateMethod', 
                null, 
                InputOption::VALUE_OPTIONAL, 
                'Path to template method.', 
                __DIR__.'/../Generators/templates/tddMethod.txt'
            ),
            array(
                'templateMethodInterface', 
                null, 
                InputOption::VALUE_OPTIONAL, 
                'Path to template method interface.', 
                __DIR__.'/../Generators/templates/tddMethodInterface.txt'
            ),
            array(
                'removeSSuffixFromTableName', 
                null, 
                InputOption::VALUE_OPTIONAL, 
                'Remove last "s" char from each table name word? Yes | No.', 
                'No'
            )
        );
    }

}
