<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Details extends Controller_Backend_Template {

	/**
	 * Before
	 */
	public function before()
	{
		parent::before();

		// Local menus
		$this->local_menus = array(
			'index' => array('name' => 'index', 'url' => URL::site("{$this->settings->backend_name}/details", 'http')),
			/**
			 * Action index
			 */			);

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
		 * Build columns
		 */
		// <editor-fold defaultstate="collapsed" desc="Build columns">
		// Get order
		$query = $this->request->query();
		$order_column = Arr::get($query, 'order_column', 'order');
		$order_direction = Arr::get($query, 'order_direction', 'ASC');

		$columns = array(
			'id' => array(
				'name' => 'id',
				'order_column' => 'id',
				'order_direction' => 'ASC',
			),
			'segment' => array(
				'name' => 'segment',
				'order_column' => 'segment',
				'order_direction' => 'ASC',
			),
			'name' => array(
				'name' => 'name',
				'order_column' => 'name',
				'order_direction' => 'ASC',
			),
			'order' => array(
				'name' => 'order',
				'order_column' => 'order',
				'order_direction' => 'ASC',
			),
			'delete' => array(
				'name' => '',
			),
		);

		foreach ($columns as &$column)
		{
			if (isset($column['order_column']))
			{
				if ($column['order_column'] == $order_column)
				{
					$column['current'] = TRUE;

					if ($order_direction == 'ASC')
					{
						$column['order_direction'] = 'DESC';
						$column['current_asc'] = TRUE;
					}
					else
					{
						$column['order_direction'] = 'ASC';
						$column['current_desc'] = TRUE;
					}
				}

				$column['url'] = URL::base(TRUE).Request::current()->uri().URL::query(array('order_column' => $column['order_column'], 'order_direction' => $column['order_direction']), FALSE);
			}
		}
		// </editor-fold>

		/**
		 * If post create
		 */
		// <editor-fold defaultstate="collapsed" desc="If post create">
		$create = array();

		// If there are post create
		if ($this->request->post('create'))
		{
			// Build data
			$create['segment'] = $this->request->post('create_segment');
			$create['name'] = $this->request->post('create_name');
			$create['order'] = $this->request->post('create_order') ? : NULL;

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Create
				$detail = Tbl::factory('details')
					->create($create);

				// Get user ids
				$user_ids = Tbl::factory('users')
					->read()
					->as_array(NULL, 'id');

				// Create users_details
				foreach ($user_ids as $user_id)
				{
					Tbl::factory('users_details')
						->create(array(
							'user_id' => $user_id,
							'detail_id' => $detail->id,
							'value' => NULL,
					));
				}

				// Database commit
				Database::instance()->commit();

				// Clear create
				$create['segment'] = NULL;
				$create['name'] = NULL;
				$create['order'] = NULL;


				// Add success notice
				Notice::add(Notice::SUCCESS, Kohana::message('general', 'create_success'));
			}
			catch (HTTP_Exception_302 $e)
			{
				$this->redirect($e->location());
			}
			catch (Validation_Exception $e)
			{
				// Database rollback
				Database::instance()->rollback();

				// Add validation notice
				Notice::add(Notice::VALIDATION, Kohana::message('general', 'create_failed'), NULL, $e->errors('validation'));
			}
			catch (Exception $e)
			{
				// Database rollback
				Database::instance()->rollback();

				// Add error notice
				Notice::add(
					Notice::ERROR, $e->getMessage()
				);
			}
		}
		// </editor-fold>

		/*
		 * Get details
		 */
		//<editor-fold defaultstate="collapsed" desc="Get details">
		$details = Tbl::factory('details')
			->order_by($order_column, $order_direction)
			->order_by('order', 'ASC')// なるべくorder順でソート
			->read()
			->as_array('id');

		foreach ($details as $detail)
		{
			$detail->rule_url = URL::site("{$this->settings->backend_name}/details/rule/{$detail->id}", 'http');
			$detail->delete_url = URL::site("{$this->settings->backend_name}/details/delete/{$detail->id}", 'http');
		}
		// </editor-fold>

		/**
		 * If post update
		 */
		// <editor-fold defaultstate="collapsed" desc="If post update">
		if ($this->request->post('update'))
		{
			$post = $this->request->post();

			// Set to detail
			foreach ($details as $detail)
			{
				$detail->segment = $post['segment'][$detail->id];
				$detail->name = $post['name'][$detail->id];
				$detail->order = $post['order'][$detail->id];
			}

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update
				foreach ($details as $detail)
				{
					Tbl::factory('details')
						->get($detail->id)
						->update(array(
							'segment' => $post['segment'][$detail->id],
							'name' => $post['name'][$detail->id],
							'order' => $post['order'][$detail->id] ? : NULL,
					));
				}

				// Database commit
				Database::instance()->commit();

				// Add success notice
				Notice::add(Notice::SUCCESS, Kohana::message('general', 'update_success'));
			}
			catch (HTTP_Exception_302 $e)
			{
				$this->redirect($e->location());
			}
			catch (Validation_Exception $e)
			{
				// Database rollback
				Database::instance()->rollback();

				// Add validation notice
				Notice::add(Notice::VALIDATION, Kohana::message('general', 'update_success'), NULL, $e->errors('validation'));
			}
			catch (Exception $e)
			{
				// Database rollback
				Database::instance()->rollback();

				// Add error notice
				Notice::add(
					Notice::ERROR, $e->getMessage()
				);
			}
		}
		// </editor-fold>

		/**
		 * View
		 */
		// <editor-fold defaultstate="collapsed" desc="View">

		$content_file = Tpl::get_file('index', $this->settings->back_tpl_dir.'/details', $this->partials);

		$this->content = Tpl::factory($content_file)
			->set('columns', $columns)
			->set('details', $details)
			->set('create', $create);
		// </editor-fold>

		/**
		 * Notice info
		 */
		Notice::add(Notice::WARNING, Kohana::message('general', 'relation_delete'), array(':text' => 'detail'));
	}

	/**
	 * Action rule
	 */
	public function action_rule()
	{
		// Get id from param, if there is nothing then throw to 404
		$id = $this->request->param('key');
		if (!$id) throw HTTP_Exception::factory(404);

		// Get detail, if there is nothing then throw to 404
		$detail = Tbl::factory('details')->get($id);
		if (!$detail) throw HTTP_Exception::factory(404);

		$create = array();

		// If there are post
		if ($this->request->post('create'))
		{
			// Build data
			$create['detail_id'] = $detail->id;
			$create['callback'] = $this->request->post('create_callback');
			$create['param'] = $this->request->post('create_param');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Create
				Tbl::factory('detail_rules')
					->create($create);

				// Database commit
				Database::instance()->commit();

				// Clear create
				$create['callback'] = NULL;
				$create['param'] = NULL;

				// Add success notice
				Notice::add(Notice::SUCCESS, Kohana::message('general', 'create_success'));
			}
			catch (HTTP_Exception_302 $e)
			{
				$this->redirect($e->location());
			}
			catch (Validation_Exception $e)
			{
				// Database rollback
				Database::instance()->rollback();

				// Add validation notice
				Notice::add(Notice::VALIDATION, Kohana::message('general', 'create_failed'), NULL, $e->errors('validation'));
			}
			catch (Exception $e)
			{
				// Database rollback
				Database::instance()->rollback();

				// Add error notice
				Notice::add(
					Notice::ERROR, $e->getMessage()
				);
			}
		}

		// Get detail rules
		$rules = Tbl::factory('detail_rules')
			->where('detail_id', '=', $detail->id)
			->read()
			->as_array();

		foreach ($rules as $rule)
		{
			$rule->delete_url = URL::site("{$this->settings->backend_name}/details/rule_delete/{$rule->id}", 'http');
		}

		// If there are post update
		if ($this->request->post('update'))
		{
			$post = $this->request->post();

			// Set post to tag
			foreach ($rules as $rule)
			{
				$rule->callback = $post['callback'][$rule->id];
				$rule->param = $post['param'][$rule->id];
			}

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update
				foreach ($rules as $rule)
				{
					Tbl::factory('detail_rules')
						->get($rule->id)
						->update(array(
							'callback' => $post['callback'][$rule->id],
							'param' => $post['param'][$rule->id],
					));
				}

				// Database commit
				Database::instance()->commit();

				// Add success notice
				Notice::add(Notice::SUCCESS, Kohana::message('general', 'update_success'));
			}
			catch (HTTP_Exception_302 $e)
			{
				$this->redirect($e->location());
			}
			catch (Validation_Exception $e)
			{
				// Database rollback
				Database::instance()->rollback();

				// Add validation notice
				Notice::add(Notice::VALIDATION, Kohana::message('general', 'update_success'), NULL, $e->errors('validation'));
			}
			catch (Exception $e)
			{
				// Database rollback
				Database::instance()->rollback();

				// Add error notice
				Notice::add(
					Notice::ERROR, $e->getMessage()
				);
			}
		}

		/**
		 * View
		 */
		// <editor-fold defaultstate="collapsed" desc="View">

		$this->partials['local_menu'] = Tpl::get_file('local_menu', $this->settings->back_tpl_dir);

		$content_file = Tpl::get_file('rule', $this->settings->back_tpl_dir.'/details', $this->partials);

		$this->content = Tpl::factory($content_file)
			->set('local_menus', $this->local_menus)
			->set('detail', $detail)
			->set('rules', $rules);
		// </editor-fold>
	}

	/**
	 * Action delete
	 */
	public function action_delete()
	{
		// Auto render off
		$this->auto_render = FALSE;

		// Get id from param, if there is nothing then throw to 404
		$id = $this->request->param('key');
		if (!$id) throw HTTP_Exception::factory(404);

		// Get detail, if there is nothing then throw to 404
		$detail = Tbl::factory('details')->get($id);
		if (!$detail) throw HTTP_Exception::factory(404);

		// Database transaction start
		Database::instance()->begin();

		// Try
		try
		{
			// Get detail_rules ids
			$detail_rule_ids = Tbl::factory('detail_rules')
				->where('detail_id', '=', $detail->id)
				->read()
				->as_array(NULL, 'id');

			// Delete detail_rules
			foreach ($detail_rule_ids as $detail_rule_id)
			{
				Tbl::factory('detail_rules')
					->where('id', '=', $detail_rule_id)
					->get()
					->delete();
			}

			// Delate users_details
			$users_details_ids = Tbl::factory('users_details')
				->where('detail_id', '=', $detail->id)
				->read()
				->as_array(NULL, 'id');

			// Delete detail_rules
			foreach ($users_details_ids as $users_details_id)
			{
				Tbl::factory('users_details')
					->where('id', '=', $users_details_id)
					->get()
					->delete();
			}

			// Delete detail
			$detail->delete();

			// Database commit
			Database::instance()->commit();

			// Add success notice
			Notice::add(Notice::SUCCESS, Kohana::message('general', 'delete_success'));
		}
		catch (HTTP_Exception_302 $e)
		{
			$this->redirect($e->location());
		}
		catch (Validation_Exception $e)
		{
			// Database rollback
			Database::instance()->rollback();

			// Add validation notice
			Notice::add(Notice::VALIDATION, Kohana::message('general', 'delete_failed'), NULL, $e->errors('validation'));
		}
		catch (Warning_Exception $e)
		{
			// Database rollback
			Database::instance()->rollback();

			// Add
			Notice::add(Notice::WARNING, $e->getMessage());
		}
		catch (Exception $e)
		{
			// Database rollback
			Database::instance()->rollback();

			// Add error notice
			Notice::add(
				Notice::ERROR//, $e->getMessage()
			);
		}

		// Redirect to wrapper edit
		$this->redirect(URL::site("{$this->settings->backend_name}/details/index", 'http'));
	}

	/**
	 * Action rule_delete
	 */
	public function action_rule_delete()
	{
		// Auto render off
		$this->auto_render = FALSE;

		// Get id from param, if there is nothing then throw to 404
		$id = $this->request->param('key');
		if (!$id) throw HTTP_Exception::factory(404);

		// Get detail, if there is nothing then throw to 404
		$detail_rule = Tbl::factory('detail_rules')->get($id);
		if (!$detail_rule) throw HTTP_Exception::factory(404);

		// Database transaction start
		Database::instance()->begin();

		// Try
		try
		{
			// Delete detail
			$detail_rule->delete();

			// Database commit
			Database::instance()->commit();

			// Add success notice
			Notice::add(Notice::SUCCESS, Kohana::message('general', 'delete_success'));
		}
		catch (HTTP_Exception_302 $e)
		{
			$this->redirect($e->location());
		}
		catch (Validation_Exception $e)
		{
			// Database rollback
			Database::instance()->rollback();

			// Add validation notice
			Notice::add(Notice::VALIDATION, Kohana::message('general', 'delete_failed'), NULL, $e->errors('validation'));
		}
		catch (Warning_Exception $e)
		{
			// Database rollback
			Database::instance()->rollback();

			// Add
			Notice::add(Notice::WARNING, $e->getMessage());
		}
		catch (Exception $e)
		{
			// Database rollback
			Database::instance()->rollback();

			// Add error notice
			Notice::add(
				Notice::ERROR//, $e->getMessage()
			);
		}

		// Redirect to wrapper edit
		$this->redirect(URL::site("{$this->settings->backend_name}/details/rule/{$detail_rule->detail_id}", 'http'));
	}

	/**
	 * After
	 */
	public function after()
	{
		parent::after();
	}

}
