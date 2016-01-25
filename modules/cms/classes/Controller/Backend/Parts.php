<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Parts extends Controller_Backend_Template {

	/**
	 * Before
	 */
	public function before()
	{
		parent::before();

		// Local menus
		$this->local_menus = array(
			'index' => array('name' => 'index', 'url' => URL::site("{$this->settings->backend_name}/parts", 'http')),
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
		// If there are post
		if ($this->request->post())
		{
			// Try
			try
			{
				// Validation
				$validation = Validation::factory($this->request->post())
					// rule
					->rule('segment', 'not_empty')
					->rule('segment', 'max_length', array(':value', '200'))
					->rule('segment', 'regex', array(':value', '/^[a-z0-9_]+$/'))
					->rule('segment', function(Validation $array, $field, $value) {
						$segments = array_keys((array) Cms_Helper::get_dirfiles('part', $this->settings->front_tpl_dir));

						if (in_array($value, $segments))
						{
							$array->error($field, 'uniquely');
						}
					 }
						, array(':validation', ':field', ':value')
					)
					// Lavel
					->label('segment', __('Segment'))
				;

				// Check validation
				if (!$validation->check())
				{
					throw new Validation_Exception($validation);
				}

				// Create file
				Cms_Helper::set_file($this->request->post('segment'), $this->settings->front_tpl_dir.'/part', 'part');

				// Clear post
				$this->request->post(array());

				// Add success notice
				Notice::add(Notice::SUCCESS, Kohana::message('general', 'create_success'));
			}
			catch (HTTP_Exception_302 $e)
			{
				$this->redirect($e->location());
			}
			catch (Validation_Exception $e)
			{
				// Add validation notice
				Notice::add(Notice::VALIDATION, Kohana::message('general', 'create_failed'), NULL, $e->errors('validation'));
			}
			catch (Exception $e)
			{
				// Add error notice
				Notice::add(
					Notice::ERROR, $e->getMessage()
				);
			}
		}

		// Get parts フォルダから取得
		$parts = Cms_Helper::get_dirfiles('part', $this->settings->front_tpl_dir);

		foreach ($parts as $part)
		{
			$part->edit_url = URL::site("{$this->settings->backend_name}/parts/edit/{$part->segment}", 'http');
		}

		/**
		 * View
		 */
		$content_file = Tpl::get_file('index', $this->settings->back_tpl_dir.'/parts', $this->partials);

		$this->content = Tpl::factory($content_file)
			->set('parts', $parts)
			->set('post', $this->request->post());
	}

	/**
	 * Action edit
	 */
	public function action_edit()
	{
		// Get id from param, if there is nothing then throw to 404
		$segment = strtolower($this->request->param('key'));
		if (!$segment) throw HTTP_Exception::factory(404);

		// Make part and get content from file and direct set to part
		$part = new stdClass;
		$part->segment = strtolower($segment);
		$part->content = Tpl::get_file($segment, $this->settings->front_tpl_dir.'/part');

		// If there is nothing then throw to 404
		if ($part->content === FALSE) throw HTTP_Exception::factory(404);

		// Set delete url
		$part->delete_url = URL::site("{$this->settings->backend_name}/parts/delete/{$part->segment}", 'http');

		// Save present segment
		$oldname = $part->segment;

		// If there are post
		if ($this->request->post())
		{
			// Try
			try
			{
				// Validation
				$validation = Validation::factory($this->request->post())
					// rule
					->rule('segment', 'not_empty')
					->rule('segment', 'max_length', array(':value', '200'))
					->rule('segment', 'regex', array(':value', '/^[a-z0-9_]+$/'))
					->rule('segment', function(Validation $array, $field, $value, $present_segment) {
						$segments = array_keys((array) Cms_Helper::get_dirfiles('part', $this->settings->front_tpl_dir));

						if (in_array($value, $segments))
						{
							if ($value !== $present_segment)
							{
								$array->error($field, 'uniquely');
							}
						}
					 }
						, array(':validation', ':field', ':value', $part->segment)
					)
					// Lavel
					->label('segment', __('Segment'))
				;

				// Check validation
				if (!$validation->check())
				{
					throw new Validation_Exception($validation);
				}

				// new name
				$newname = $this->request->post('segment');

				// Update file これが先じゃないとダメ
				Cms_Helper::set_file($oldname, "{$this->settings->front_tpl_dir}/part", $this->request->post('content'));

				// rename file
				Cms_Helper::rename_file($oldname, $newname, "{$this->settings->front_tpl_dir}/part");

				// Add success notice
				Notice::add(Notice::SUCCESS, Kohana::message('general', 'update_success'));

				// Redirect
				$this->redirect("{$this->settings->backend_name}/parts/edit/{$newname}");
			}
			catch (HTTP_Exception_302 $e)
			{
				$this->redirect($e->location());
			}
			catch (Validation_Exception $e)
			{
				// Add validation notice
				Notice::add(Notice::VALIDATION, Kohana::message('general', 'update_failed'), NULL, $e->errors('validation'));
			}
			catch (Exception $e)
			{
				// Add error notice
				Notice::add(
					Notice::ERROR//, $e->getMessage()
				);
			}
		}

		/**
		 * View
		 */
		$content_file = Tpl::get_file('edit', $this->settings->back_tpl_dir.'/parts', $this->partials);

		$this->content = Tpl::factory($content_file)
			->set('part', $part);
	}

	/**
	 * Action delete
	 */
	public function action_delete()
	{
		// Auto render off
		$this->auto_render = FALSE;

		// Get id from param, if there is nothing then throw to 404
		$segment = $this->request->param('key');
		if (!$segment) throw HTTP_Exception::factory(404);

		// Make part and get content from file and direct set to part
		$part = new stdClass;
		$part->segment = $segment;
		$part->content = Tpl::get_file($segment, $this->settings->front_tpl_dir.'/part');

		// If there is nothing then throw to 404
		if ($part->content === FALSE) throw HTTP_Exception::factory(404);

		// Try
		try
		{
			/**
			 * Delete
			 */
			// Delete file
			Cms_Helper::delete_file($part->segment, "{$this->settings->front_tpl_dir}/part");

			// Add success notice
			Notice::add(Notice::SUCCESS, Kohana::message('general', 'delete_success'));

			$this->redirect(URL::site("{$this->settings->backend_name}/parts/index", 'http'));
		}
		catch (HTTP_Exception_302 $e)
		{
			$this->redirect($e->location());
		}
		catch (Validation_Exception $e)
		{
			// Add validation notice
			Notice::add(Notice::VALIDATION, Kohana::message('general', 'delete_failed'), NULL, $e->errors('validation'));
		}
		catch (Exception $e)
		{
			// Add error notice
			Notice::add(
				Notice::ERROR//, $e->getMessage()
			);
		}

		// Redirect to wrapper edit
		$this->redirect(URL::site("{$this->settings->backend_name}/parts/edit/{$part->segment}", 'http'));
	}

	/**
	 * After
	 */
	public function after()
	{
		parent::after();
	}

}
