<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Home extends Controller_Backend_Template {

	/**
	 * Before
	 */
	public function before()
	{
		parent::before();

		// Local menus
		$this->local_menus = array(
			'index' => array('name' => 'index', 'url' => URL::site("{$this->settings->backend_name}/home", 'http')),
			'syntax' => array('name' => 'syntax', 'url' => URL::site("{$this->settings->backend_name}/home/syntax", 'http')),
			'about' => array('name' => 'about', 'url' => URL::site("{$this->settings->backend_name}/home/about", 'http')),
			'tutorial' => array('name' => 'tutorial', 'url' => URL::site("{$this->settings->backend_name}/home/tutorial", 'http')),
		);

		// local menus set current
		foreach ($this->local_menus as $key => &$value)
		{
			if ($key == strtolower($this->request->action()))
			{
				$value['current'] = TRUE;
			}
		}

		// Set partial
		$this->partials['local_menu'] = Tpl::get_file('local_menu', $this->settings->back_tpl_dir);
	}

	/**
	 * Action index
	 */
	public function action_index()
	{
		/**
		 * View
		 */
		// Get content file and Factory
		$content_file = Tpl::get_file('index', $this->settings->back_tpl_dir.'/home', $this->partials);
		$this->content = Tpl::factory($content_file);
	}

	/**
	 * Action about
	 */
	public function action_about()
	{
		/**
		 * View
		 */
		// Get content file and Factory
		$content_file = Tpl::get_file('about', $this->settings->back_tpl_dir.'/home', $this->partials);
		$this->content = Tpl::factory($content_file);
	}

	/**
	 * Action syntax
	 */
	public function action_syntax()
	{
		/**
		 * View
		 */
		// Get content file and Factory
		$content_file = Tpl::get_file('syntax', $this->settings->back_tpl_dir.'/home', $this->partials);
		$this->content = Tpl::factory($content_file);
	}

	/**
	 * Action tutorial
	 */
	public function action_tutorial()
	{
		$file = 'tutorial'.$this->request->param('key', 1);
		/**
		 * View
		 */
		// Get content file and Factory
		$content_file = Tpl::get_file($file, $this->settings->back_tpl_dir.'/home', $this->partials);
		$this->content = Tpl::factory($content_file);
	}

	/**
	 * After
	 */
	public function after()
	{
		parent::after();
	}

}
