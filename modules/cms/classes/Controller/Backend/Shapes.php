<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Shapes extends Controller_Backend_Template {

	/**
	 * Before
	 */
	public function before()
	{
		parent::before();

		// Local menus
		$this->local_menus = array(
			'index' => array('name' => 'index', 'url' => URL::site("{$this->settings->backend_name}/shapes", 'http')),
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
						$segments = array_keys((array) Cms_Helper::get_dirfiles('shape', $this->settings->front_tpl_dir));

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
				Cms_Helper::set_file($this->request->post('segment'), $this->settings->front_tpl_dir.'/shape', 'shape');

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

		// Get shape フォルダから取得
		$shapes = Cms_Helper::get_dirfiles('shape', $this->settings->front_tpl_dir);

		foreach ($shapes as $shape)
		{
			$shape->edit_url = URL::site("{$this->settings->backend_name}/shapes/edit/{$shape->segment}", 'http');
		}

		/**
		 * View
		 */
		$content_file = Tpl::get_file('index', $this->settings->back_tpl_dir.'/shapes', $this->partials);

		$this->content = Tpl::factory($content_file)
			->set('shapes', $shapes)
			->set('post', $this->request->post());
	}

	/**
	 * Action edit
	 */
	public function action_edit()
	{
		// Get id from param, if there is nothing then throw to 404
		$segment = $this->request->param('key');
		if (!$segment) throw HTTP_Exception::factory(404);

		// Make shape and get content from file and direct set to shape
		$shape = new stdClass;
		$shape->segment = strtolower($segment);
		$shape->content = Tpl::get_file($shape->segment, $this->settings->front_tpl_dir.'/shape');

		// If there is nothing then throw to 404
		if ($shape->content === FALSE) throw HTTP_Exception::factory(404);

		// Set delete url
		$shape->delete_url = URL::site("{$this->settings->backend_name}/shapes/delete/{$shape->segment}", 'http');

		// Save present segment
		$oldname = $shape->segment;

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
						$segments = array_keys((array) Cms_Helper::get_dirfiles('shape', $this->settings->front_tpl_dir));

						if (in_array($value, $segments))
						{
							if ($value !== $present_segment)
							{
								$array->error($field, 'uniquely');
							}
						}
					 }
						, array(':validation', ':field', ':value', $shape->segment)
					)
					// Lavel
					->label('segment', __('Segment'))
				;

				// Check validation
				if (!$validation->check())
				{
					throw new Validation_Exception($validation);
				}

				// New file
				$newname = $this->request->post('segment');

				// Update file これが先じゃないとダメ
				Cms_Helper::set_file($oldname, "{$this->settings->front_tpl_dir}/shape", $this->request->post('content'));

				// rename file
				Cms_Helper::rename_file($oldname, $newname, "{$this->settings->front_tpl_dir}/shape");

				// Add success notice
				Notice::add(Notice::SUCCESS, Kohana::message('general', 'update_success'));

				// Redirect
				$this->redirect("{$this->settings->backend_name}/shapes/edit/{$newname}");
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
		$content_file = Tpl::get_file('edit', $this->settings->back_tpl_dir.'/shapes', $this->partials);

		$this->content = Tpl::factory($content_file)
			->set('shape', $shape);
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

		// Make shape and get content from file and direct set to shape
		$shape = new stdClass;
		$shape->segment = $segment;
		$shape->content = Tpl::get_file($segment, $this->settings->front_tpl_dir.'/shape');

		// If there is nothing then throw to 404
		if ($shape->content === FALSE) throw HTTP_Exception::factory(404);

		// Try
		try
		{
			/**
			 * Check other tables
			 */
			// used by items
			$used_items = (bool) Tbl::factory('items')
					->where('shape_segment', '=', $shape->segment)
					->read()
					->count();

			// If this shape is used throw to warning
			if ($used_items) throw new Warning_Exception(Kohana::message('general', 'shape_is_used'));

			/**
			 * Delete
			 */
			// Delete file
			Cms_Helper::delete_file($shape->segment, "{$this->settings->front_tpl_dir}/shape");

			// Add success notice
			Notice::add(Notice::SUCCESS, Kohana::message('general', 'delete_success'));

			$this->redirect(URL::site("{$this->settings->backend_name}/shapes/index", 'http'));
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
		catch (Warning_Exception $e)
		{
			// Add
			Notice::add(Notice::WARNING, $e->getMessage());
		}
		catch (Exception $e)
		{
			// Add error notice
			Notice::add(
				Notice::ERROR, $e->getMessage().' : '.$e->getFile().' : '.$e->getLine()
			);
		}

		// Redirect to wrapper edit
		$this->redirect(URL::site("{$this->settings->backend_name}/shapes/edit/{$shape->segment}", 'http'));
	}

	/**
	 * After
	 */
	public function after()
	{
		parent::after();
	}

}
