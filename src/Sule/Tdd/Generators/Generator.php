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
     * @param  string  $classSuffix
     * @param  boolean $removeSSuffixFromTableName
     * @return array
     */
    public function make($path, $classSuffix, $removeSSuffixFromTableName = false)
    {
        $this->tables = $this->getTables();

        $errors = array();

        if ( ! empty($this->tables)) {
            foreach ($this->tables as $table) {
                $errors = array_merge(
                    $errors, 
                    $this->makeItem($path, current($table), $classSuffix, $removeSSuffixFromTableName)
                );
            }
        } else {
            $errors[] = 'No table could be found in current database';
        }

        return $errors;
    }

    /**
     * Compile a template and generate
     *
     * @param  string $path
     * @param  string $name
     * @param  string  $classSuffix
     * @param  boolean $removeSSuffixFromTableName
     * @return array
     */
    public function makeItem($path, $name, $classSuffix, $removeSSuffixFromTableName = false)
    {
        $className = $this->formatName($name, $removeSSuffixFromTableName);

        $data = array(
            'model'       => $className,
            'className'   => $className,
            'classSuffix' => $classSuffix
        );

        $template                = $this->getTemplate($data);
        $templateInterface       = $this->getTemplateInterface($data);
        $templateMethod          = '';
        $templateMethodInterface = '';

        $columns = $this->getColumns($name);

        // Generate all the methods
        if ( ! empty($columns)) {
            foreach ($columns as $column) {
                $data = array(
                    'column'       => $column->Field,
                    'columnMethod' => $this->formatName($column->Field)
                );

                $templateMethod          .= $this->getTemplateMethod($data)."\n";
                $templateMethodInterface .= $this->getTemplateMethodInterface($data)."\n";
            }
        }

        unset($columns);

        $template = $this->compileTemplate($template, array(
            'methods' => $templateMethod
        ));

        $templateInterface = $this->compileTemplate($templateInterface, array(
            'methods' => $templateMethodInterface
        ));

        unset($templateMethod);
        unset($templateMethodInterface);

        $errors = array();

        $filePath = $path.'/'.$className.$classSuffix.'.php';
        
        if ( ! $this->getFile()->exists($filePath)) {
            if (false === $this->getFile()->put($filePath, $template)) {
                $errors[] = 'Unable to create '.$filePath;
            }
        } else {
            $errors[] = $filePath.' is already exist.';
        }

        $filePath = $path.'/'.$className.'Interface'.$classSuffix.'.php';

        if ( ! $this->getFile()->exists($filePath)) {
            if (false === $this->getFile()->put($filePath, $templateInterface)) {
                $errors[] = 'Unable to create '.$filePath;
            }
        } else {
            $errors[] = $filePath.' is already exist.';
        }

        return $errors;
    }

    /**
     * Convert table name into a class name
     *
     * @param  string  $name
     * @param  boolean $removeSSuffixFromTableName
     * @return string
     */
    protected function formatName($name, $removeSSuffixFromTableName = false)
    {
        $name = ucwords(str_replace('_', ' ', $name));

        $words = explode(' ', $name);

        if ($removeSSuffixFromTableName) {
            foreach ($words as $index => $word) {
                $words[$index] = rtrim($word, 's');
            }
        }

        $words[0] = strtolower($words[0]);

        $name = implode(' ', $words);

        unset($words);

        return str_replace(' ', '', $name);
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
     * Get all available column in a table
     *
     * @param  string $tableName
     * @return array
     */
    protected function getColumns($tableName)
    {
        $sql = 'SHOW FIELDS FROM '.$tableName;

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