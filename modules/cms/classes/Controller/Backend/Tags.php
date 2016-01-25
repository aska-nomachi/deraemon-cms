<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Tags extends Controller_Backend_Template {

	/**
	 * Before
	 */
	public function before()
	{
		parent::before();

		// Local menus
		$this->local_menus = array(
			'index' => array('name' => 'index', 'url' => URL::site("{$this->settings->backend_name}/tags", 'http')),
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
			$create['order'] = $this->request->post('create_order');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Create
				Tbl::factory('tags')
					->create($create);

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

		/**
		 * Get tags
		 */
		// <editor-fold defaultstate="collapsed" desc="Get tags">
		$tags = Tbl::factory('tags')
			->order_by($order_column, $order_direction)
			->read()
			->as_array();

		foreach ($tags as $tag)
		{
			$tag->delete_url = URL::site("{$this->settings->backend_name}/tags/delete/{$tag->id}", 'http');
		}
		// </editor-fold>

		/**
		 * If post update
		 */
		// <editor-fold defaultstate="collapsed" desc="If post update">
		if ($this->request->post('update'))
		{
			$post = $this->request->post();

			// Set post to tag
			foreach ($tags as $tag)
			{
				$tag->segment = $post['segment'][$tag->id];
				$tag->name = $post['name'][$tag->id];
				$tag->order = $post['order'][$tag->id];
			}

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update
				foreach ($tags as $tag)
				{
					Tbl::factory('tags')
						->get($tag->id)
						->update(array(
							'segment' => $post['segment'][$tag->id],
							'name' => $post['name'][$tag->id],
							'order' => $post['order'][$tag->id],
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
		$content_file = Tpl::get_file('index', $this->settings->back_tpl_dir.'/tags', $this->partials);

		$this->content = Tpl::factory($content_file)
			->set('columns', $columns)
			->set('tags', $tags)
			->set('create', $create);
		// </editor-fold>

		/**
		 * Notice info
		 */
		Notice::add(Notice::WARNING, Kohana::message('general', 'relation_delete'), array(':text' => 'tag'));
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

		// Get tag, if there is nothing then throw to 404
		$tag = Tbl::factory('tags')->get($id);
		if (!$tag) throw HTTP_Exception::factory(404);

		// Database transaction start
		Database::instance()->begin();

		// Try
		try
		{
			/**
			 * Delete
			 */
			// Get items_tags ids has this tag id このfieldのidを持つitems_tagsを取得
			$items_tags_ids = Tbl::factory('items_tags')
				->where('tag_id', '=', $tag->id)
				->read()
				->as_array(NULL, 'id');

			// Delete items_tags
			foreach ($items_tags_ids as $items_tags_id)
			{
				Tbl::factory('items_tags')
					->where('id', '=', $items_tags_id)
					->get()
					->delete();
			}

			// Delete
			$tag->delete();

			// Database commit
			Database::instance()->commit();

			// Add success notice
			Notice::add(Notice::SUCCESS, Kohana::message('general', 'delete_success'));

			$this->redirect(URL::site("{$this->settings->backend_name}/tags/index", 'http'));
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
		$this->redirect(URL::site("{$this->settings->backend_name}/tags/index", 'http'));
	}

	/**
	 * After
	 */
	public function after()
	{
		parent::after();
	}

}
