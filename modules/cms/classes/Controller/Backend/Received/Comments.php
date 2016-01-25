<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Received_Comments extends Controller_Backend_Template {

	/**
	 * Before
	 */
	public function before()
	{
		parent::before();

		// Local menus
		$this->local_menus = array(
			'index' => array('name' => 'index', 'url' => URL::site("{$this->settings->backend_name}/received-comments", 'http')),
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
		/*
		 * Build columns
		 */
		// <editor-fold defaultstate="collapsed" desc="Build columns">
		// Get order
		$query = $this->request->query();
		$order_column = Arr::get($query, 'order_column', 'created');
		$order_direction = Arr::get($query, 'order_direction', 'DESC');

		// Build columns
		$columns = array(
			array(
				'name' => 'id',
				'order_column' => 'id',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'item segment',
				'order_column' => 'item_segment',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'item title',
				'order_column' => 'item_title',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'item id',
				'order_column' => 'item_id',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'user id',
				'order_column' => 'user_id',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'replay id',
				'order_column' => 'replay_id',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'display name',
				'order_column' => 'display_name',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'subject',
				'order_column' => 'subject',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'created',
				'order_column' => 'created',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'is accept',
				'order_column' => 'is_accept',
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
				// Get delete received comment ids
				$delete_received_comment_ids = Arr::get($this->request->post(), 'delete_received_comment_id', array());

				// Iterate and chack and delete
				foreach ($delete_received_comment_ids as $delete_received_comment_id)
				{
					// Get received comment
					$received_comment = Tbl::factory('received_comments')->get($delete_received_comment_id);

					// Delete
					$received_comment->delete();
				}

				// Database commit
				Database::instance()->commit();

				// Add success notice
				if ($delete_received_comment_ids)
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
		 * Get received_comments
		 */
		// <editor-fold defaultstate="collapsed" desc="Get received_comments">
		$all_received_comments = Tbl::factory('received_comments')
			->select('received_comments.*')
			->select(array('items.segment', 'item_segment'))
			->select(array('items.title', 'item_title'))
			->join('items')->on('received_comments.item_id', '=', 'items.id')
			->order_by($order_column, $order_direction)
			->read()
			->as_array();

		$pagenate = Pgn::factory(array(
				'total_items' => count($all_received_comments),
				'items_per_page' => $this->settings->pagenate_items_per_page_for_received_comments,
				'follow' => $this->settings->pagenate_items_follow_for_received_comments,
		));

		// Paginated items
		$received_comments = array_slice($all_received_comments, $pagenate->offset, $pagenate->items_per_page);

		foreach ($received_comments as $received_comment)
		{
			$received_comment->delete_url = URL::site("{$this->settings->backend_name}/received_comments/delete/{$received_comment->id}", 'http').URL::query();
		}
		// </editor-fold>

		/*
		 * If update
		 */
		// <editor-fold defaultstate="collapsed" desc="If update">
		if ($this->request->post('update'))
		{
			$post = $this->request->post();

			// Set post to tag
			foreach ($received_comments as $received_comment)
			{
				$received_comment->is_accept = isset($post['is_accept'][$received_comment->id]) ? $post['is_accept'][$received_comment->id] : 0;
			}

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update
				foreach ($received_comments as $received_comment)
				{
					Tbl::factory('received_comments')
						->get($received_comment->id)
						->update(array(
							'is_accept' => isset($post['is_accept'][$received_comment->id]) ? $post['is_accept'][$received_comment->id] : 0,
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
		$this->partials['pagenate'] = Tpl::get_file('pagenate', $this->settings->back_tpl_dir);

		$content_file = Tpl::get_file('index', $this->settings->back_tpl_dir.'/received_comments', $this->partials);

		$this->content = Tpl::factory($content_file)
			->set('columns', $columns)
			->set('received_comments', $received_comments)
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
		$comment = Tbl::factory('received_comments')->get($id);
		if (!$comment) throw HTTP_Exception::factory(404);

		// Database transaction start
		Database::instance()->begin();

		// Try
		try
		{
			/**
			 * Delete
			 */
			// Delete
			$comment->delete();

			// Database commit
			Database::instance()->commit();

			// Add success notice
			Notice::add(Notice::SUCCESS, Kohana::message('general', 'delete_success'));

			// redirect
			$this->redirect(URL::site("{$this->settings->backend_name}/received_comments/index", 'http'));
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

		// Redirect to received_comments edit
		$this->redirect(URL::site("{$this->settings->backend_name}/received_comments/edit/{$comment->id}", 'http').URL::query());
	}

	/**
	 * After
	 */
	public function after()
	{
		parent::after();
	}

}
