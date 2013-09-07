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
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('sule/tdd');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		
	}

	/**
     * Register the artisan commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        
    }

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('tdd');
	}

}