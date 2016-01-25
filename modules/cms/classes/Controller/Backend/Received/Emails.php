<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Received_Emails extends Controller_Backend_Template {

	/**
	 * Before
	 */
	public function before()
	{
		parent::before();

		// Local menus
		$this->local_menus = array(
			'index' => array('name' => 'index', 'url' => URL::site("{$this->settings->backend_name}/received-emails", 'http')),
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
		// Get order
		$query = $this->request->query();
		$order_column = Arr::get($query, 'order_column', 'created');
		$order_direction = Arr::get($query, 'order_direction', 'DESC');

		/*
		 * Build columns
		 */
		// <editor-fold defaultstate="collapsed" desc="uild columns">
		$columns = array(
			array(
				'name' => 'id',
				'order_column' => 'id',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'email segment',
				'order_column' => 'email_segment',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'created',
				'order_column' => 'created',
				'order_direction' => 'ASC',
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

		/*
		 * If delete
		 */
		// <editor-fold defaultstate="collapsed" desc="If delete">
		if ($this->request->post('delete'))
		{
			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Get delete received email ids
				$delete_received_email_ids = Arr::get($this->request->post(), 'delete_received_email_id', array());

				// Iterate and chack and delete
				foreach ($delete_received_email_ids as $delete_received_email_id)
				{
					// Get received email
					$received_email = Tbl::factory('received_emails')->get($delete_received_email_id);

					// Delete
					$received_email->delete();
				}

				// Database commit
				Database::instance()->commit();

				// Add success notice
				if ($delete_received_email_ids)
				{
					Notice::add(Notice::SUCCESS, Kohana::message('general', 'delete_success'));
				}
				else
				{
					Notice::add(Notice::SUCCESS, Kohana::message('general', 'no_delete'), array(':text' => 'emil'));
				}
			}
			catch (HTTP_Exception_302 $e)
			{
				$this->redirect($e->location());
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
					Notice::ERROR, $e->getMessage().$e->getFile().$e->getLine()
				);
			}
		}
		// </editor-fold>

		/*
		 * Get received emails
		 */
		// <editor-fold defaultstate="collapsed" desc="Get received emails">
		$all_received_emails = Tbl::factory('received_emails')
			->order_by($order_column, $order_direction)
			->read()
			->as_array();

		$pagenate = Pgn::factory(array(
				'total_items' => count($all_received_emails),
				'items_per_page' => $this->settings->pagenate_items_per_page_for_received_emails,
				'follow' => $this->settings->pagenate_items_follow_for_received_emails,
		));

		// Paginated items
		$received_emails = array_slice($all_received_emails, $pagenate->offset, $pagenate->items_per_page);

		foreach ($received_emails as $received_email)
		{
			$received_email->objects = array();

			$json = json_decode($received_email->json);

			foreach ($json as $key => $value)
			{
				$received_email->objects[] = array('key' => str_replace('_', ' ', $key), 'value' => $value);
			}
			
			$email_name = Tbl::factory('emails')->select('name')->where('segment', '=', $received_email->email_segment)->read(TRUE)->name;
			$received_email->email_name = $email_name;
			$received_email->created = Date::formatted_time($received_email->created, 'Y-n-j h:i');
			$received_email->delete_url = URL::site("{$this->settings->backend_name}/received_emails/delete/{$received_email->id}", 'http');
					}
		// </editor-fold>

		/**
		 * View
		 */
		// <editor-fold defaultstate="collapsed" desc="View">
		$this->partials['pagenate'] = Tpl::get_file('pagenate', $this->settings->back_tpl_dir);

		$content_file = Tpl::get_file('index', $this->settings->back_tpl_dir.'/received_emails', $this->partials);

		$this->content = Tpl::factory($content_file)
		->set('columns', $columns)
		->set('received_emails', $received_emails)
		->set('pagenate', $pagenate)
		->set('post', $this->request->post());
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

		// Get comment, if there is nothing then throw to 404
		$received_email = Tbl::factory('received_emails')->get($id);
		if (!$received_email) throw HTTP_Exception::factory(404);

		// Database transaction start
		Database::instance()->begin();

		// Try
		try
		{
			/**
			 * Delete
			 */
			$received_email->delete();

			// Database commit
			Database::instance()->commit();

			// Add success notice
			Notice::add(Notice::SUCCESS, Kohana::message('general', 'delete_success'));

			$this->redirect(URL::site("{$this->settings->backend_name}/received_emails/index", 'http'));
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
		catch (Exception $e)
		{
			// Database rollback
			Database::instance()->rollback();

			// Add error notice
			Notice::add(
				Notice::ERROR//, $e->getMessage()
			);
		}

		// Redirect to received_emails index
		$this->redirect(URL::site("{$this->settings->backend_name}/received_emails/index", 'http'));
	}

	/**
	 * After
	 */
	public function after()
	{
		parent::after();
	}

}
