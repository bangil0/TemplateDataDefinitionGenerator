<?php
namespace Sule\Tdd\Generators;

use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Filesystem\Filesystem as File;

class Generator
{

    /**
     * The Database Manager.
     *
     * @var Illuminate\Database\ConnectionResolverInterface
     */
    protected $db;

    /**
     * File system instance
     *
     * @var Illuminate\Filesystem\Filesystem
     */
    protected $file;

    /**
     * The TDD template path
     *
     * @var string
     */
    protected $templatePath;

    /**
     * The TDD template interface path
     *
     * @var string
     */
    protected $templateInterfacePath;

    /**
     * The TDD template method path
     *
     * @var string
     */
    protected $templateMethodPath;

    /**
     * The TDD template method interface path
     *
     * @var string
     */
    protected $templateMethodInterfacePath;

    /**
     * The compiled TDD template
     *
     * @var string
     */
    protected $template;

    /**
     * The compiled TDD template interface
     *
     * @var string
     */
    protected $templateInterface;

    /**
     * The compiled TDD template method
     *
     * @var string
     */
    protected $templateMethod;

    /**
     * The compiled TDD template method interface
     *
     * @var string
     */
    protected $templateMethodInterface;

    /**
     * The table list
     *
     * @var array
     */
    protected $tables;

    /**
     * Constructor
     *
     * @param  Illuminate\Database\ConnectionResolverInterface $db
     * @param  Illuminate\Filesystem\Filesystem $file
     * @return void
     */
    public function __construct(ConnectionResolverInterface $db, File $file)
    {
        $this->db   = $db;
        $this->file = $file;
    }

    /**
     * Set all the required template paths.
     *
     * @param  string $template
     * @param  string $templateInterface
     * @param  string $templateMethod
     * @param  string $templateMethodInterface
     * @return void
     */
    public function setTemplates(
        $template, 
        $templateInterface, 
        $templateMethod, 
        $templateMethodInterface
    )
    {
        $this->templatePath                = $template;
        $this->templateInterfacePath       = $templateInterface;
        $this->templateMethodPath          = $templateMethod;
        $this->templateMethodInterfacePath = $templateMethodInterface;
    }

    /**
     * Get the Database Manager.
     *
     * @return Illuminate\Database\ConnectionResolverInterface
     */
    public function getDB()
    {
        return $this->db;
    }

    /**
     * Get the Filesystem.
     *
     * @return Illuminate\Filesystem\Filesystem
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get the template path.
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * Get the template interface path.
     *
     * @return string
     */
    public function getTemplateInterfacePath()
    {
        return $this->templateInterfacePath;
    }

    /**
     * Get the template method path.
     *
     * @return string
     */
    public function getTemplateMethodPath()
    {
        return $this->templateMethodPath;
    }

    /**
     * Get the template method interface path.
     *
     * @return string
     */
    public function getTemplateMethodInterfacePath()
    {
        return $this->templateMethodInterfacePath;
    }

    /**
     * Compile templates and generate
     *
     * @param  string  $path
     * @param  boolean $removeSSuffixFromTableName
     * @return boolean
     */
    public function make($path, $removeSSuffixFromTableName = false)
    {
        $this->tables = $this->getTables();

        if ( ! empty($this->tables)) {
            foreach ($this->tables as $table) {
                $name = current($table);
                $name = ucwords(str_replace('_', ' ', $name));

                if ($removeSSuffixFromTableName) {
                    $words = explode(' ', $name);

                    foreach ($words as $index => $word) {
                        $words[$index] = rtrim($word, 's');
                    }

                    $name = implode(' ', $words);

                    unset($words);
                }

                $name = str_replace(' ', '', $name);

                $this->makeItem($path, $name);
            }
        }

        // $this->name = basename($path, '.php');
        // $this->path = $this->getPath($path);
        // $template   = $this->getTemplate($template, $this->name);

        // if ( ! $this->file->exists($this->path)) {
        //     return $this->file->put($this->path, $template) !== false;
        // }

        return false;
    }

    /**
     * Compile a template and generate
     *
     * @param  string $path
     * @param  string $name
     * @return boolean
     */
    public function makeItem($path, $name)
    {
        $data = array(
            'model' => $name,
            'className' => $name
        );

        $template                = $this->getTemplate($data);
        $templateInterface       = $this->getTemplateInterface($data);
        $templateMethod          = $this->getTemplateMethod($data);
        $templateMethodInterface = $this->getTemplateMethodInterface($data);

        return false;
    }

    /**
     * Get all available tables in database
     *
     * @return array
     */
    protected function getTables()
    {
        $sql = 'SHOW TABLES';

        return $this->getDB()->select($sql);
    }

    /**
     * Get the path to the file
     * that should be generated
     *
     * @param  string $path
     * @return string
     */
    protected function getPath($path)
    {
        // By default, we won't do anything, but
        // it can be overridden from a child class
        return $path;
    }

    /**
     * Fetch the compiled template
     *
     * @param  array  $data
     * @return string Compiled template
     */
    protected function getTemplate(Array $data)
    {
        $this->template = $this->getFile()->get($this->getTemplatePath());

        return $this->compileTemplate($this->template, $data);
    }

    /**
     * Fetch the compiled template interface
     *
     * @param  array  $data
     * @return string Compiled template
     */
    protected function getTemplateInterface(Array $data)
    {
        $this->templateInterface = $this->getFile()->get($this->getTemplateInterfacePath());

        return $this->compileTemplate($this->templateInterface, $data);
    }

    /**
     * Fetch the compiled template method
     *
     * @param  array  $data
     * @return string Compiled template
     */
    protected function getTemplateMethod(Array $data)
    {
        $this->templateMethod = $this->getFile()->get($this->getTemplateMethodPath());

        return $this->compileTemplate($this->templateMethod, $data);
    }

    /**
     * Fetch the compiled template method interface
     *
     * @param  array  $data
     * @return string Compiled template
     */
    protected function getTemplateMethodInterface(Array $data)
    {
        $this->templateMethodInterface = $this->getFile()->get($this->getTemplateMethodInterfacePath());

        return $this->compileTemplate($this->templateMethodInterface, $data);
    }

    /**
     * Compile all specified keys with values
     *
     * @param  string $template
     * @param  array  $data
     * @return string Compiled template
     */
    protected function compileTemplate($template, Array $data)
    {
        foreach ($data as $key => $value) {
            $template = str_replace('{{'.$key.'}}', $value, $template);
        }
        
        return $template;
    }

}