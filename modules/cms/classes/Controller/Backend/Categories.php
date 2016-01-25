<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Categories extends Controller_Backend_Template {

	/**
	 * Before
	 */
	public function before()
	{
		parent::before();

		// Local menus
		$this->local_menus = array(
			'index' => array('name' => 'index', 'url' => URL::site("{$this->settings->backend_name}/categories", 'http')),
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
		// Get divisions
		$divisions = Tbl::factory('divisions')
			->read()
			->as_array();

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
			'division_name' => array(
				'name' => 'division name',
				'order_column' => 'division_name',
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
			$create['division_id'] = $this->request->post('create_division_id');
			$create['segment'] = $this->request->post('create_segment');
			$create['name'] = $this->request->post('create_name');
			$create['order'] = $this->request->post('create_order');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Create
				Tbl::factory('categories')
					->create($create);

				// Database commit
				Database::instance()->commit();

				// Clear create
				$create['division_id'] = NULL;
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
		 * Get categories
		 */
		// <editor-fold defaultstate="collapsed" desc="Get categories">
		$categories = Tbl::factory('categories')
			->select('categories.*')
			->select(array('divisions.segment', 'division_segment'))
			->select(array('divisions.name', 'division_name'))
			->join('divisions')->on('categories.division_id', '=', 'divisions.id')
			->order_by($order_column, $order_direction)
			->read()
			->as_array();

		foreach ($categories as $category)
		{
			$category->delete_url = URL::site("{$this->settings->backend_name}/categories/delete/{$category->id}", 'http');
		}
		// </editor-fold>

		/**
		 * If post update
		 */
		// <editor-fold defaultstate="collapsed" desc="If post update">
		if ($this->request->post('update'))
		{
			$post = $this->request->post();

			// Set post to category
			foreach ($categories as $category)
			{
				$category->segment = $post['segment'][$category->id];
				$category->name = $post['name'][$category->id];
				$category->order = $post['order'][$category->id];
			}

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update
				foreach ($categories as $category)
				{
					Tbl::factory('categories')
						->get($category->id)
						->update(array(
							'segment' => $post['segment'][$category->id],
							'name' => $post['name'][$category->id],
							'order' => $post['order'][$category->id],
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
		$content_file = Tpl::get_file('index', $this->settings->back_tpl_dir.'/categories', $this->partials);

		$this->content = Tpl::factory($content_file)
			->set('columns', $columns)
			->set('categories', $categories)
			->set('divisions', $divisions)
			->set('create', $create);
		// </editor-fold>

		/**
		 * Notice info
		 */
		Notice::add(Notice::WARNING, Kohana::message('general', 'relation_delete'), array(':text' => 'category'));
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

		// Get category, if there is nothing then throw to 404
		$category = Tbl::factory('categories')->get($id);
		if (!$category) throw HTTP_Exception::factory(404);

		// Database transaction start
		Database::instance()->begin();

		// Try
		try
		{
			// Get items_categories ids has this category id このcategoryのidを持つitems_categoriesを取得
			$items_categories_ids = Tbl::factory('items_categories')
				->where('category_id', '=', $category->id)
				->read()
				->as_array(NULL, 'id');

			// Delete items_categories
			foreach ($items_categories_ids as $items_categories_id)
			{
				Tbl::factory('items_categories')
					->where('id', '=', $items_categories_id)
					->get()
					->delete();
			}

			// Delete category
			$category->delete();

			// Database commit
			Database::instance()->commit();

			// Add success notice
			Notice::add(Notice::SUCCESS, Kohana::message('general', 'delete_success'));

			$this->redirect(URL::site("{$this->settings->backend_name}/categories/index", 'http'));
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
		$this->redirect(URL::site("{$this->settings->backend_name}/categories/index", 'http'));
	}

	/**
	 * After
	 */
	public function after()
	{
		parent::after();
	}

}
