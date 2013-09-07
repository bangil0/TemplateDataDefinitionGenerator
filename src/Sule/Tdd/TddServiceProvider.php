<?php
namespace Sule\Tdd;

/*
 * This file is part of the Template Data Definition Generator
 *
 * Author: Sulaeman <me@sulaeman.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Support\ServiceProvider;

class TddServiceProvider extends ServiceProvider
{

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerTddGenerator();
		$this->registerCommands();
	}

	/**
	 * Register generate:tdd
	 *
	 * @return Commands\TddGeneratorCommand
	 */
	protected function registerTddGenerator()
	{
		$this->app['generate.tdd'] = $this->app->share(function($app)
		{
			$generator = new Generators\Generator($app['db'], $app['files']);

			return new Commands\TddGeneratorCommand($generator);
		});
	}

	/**
     * Register the artisan commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        $this->commands('generate.tdd');
    }

}