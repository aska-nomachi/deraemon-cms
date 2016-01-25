<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Fields extends Controller_Backend_Template {

	/**
	 * Before
	 */
	public function before()
	{
		parent::before();

		// Local menus
		$this->local_menus = array(
			'index' => array('name' => 'index', 'url' => URL::site("{$this->settings->backend_name}/fields", 'http')),
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
		$order_column = Arr::get($query, 'order_column', 'division_name');
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
			$create['order'] = $this->request->post('create_order') ? : NULL;

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Create
				$field = Tbl::factory('fields')
					->create($create);

				// Get item has this division_id このfieldのdivision_idを持つitemを取得
				$item_ids = Tbl::factory('items')
					->where('division_id', '=', $field->division_id)
					->read()
					->as_array(NULL, 'id');

				// Create items_fields
				foreach ($item_ids as $item_id)
				{
					Tbl::factory('items_fields')
						->create(array(
							'item_id' => $item_id,
							'field_id' => $field->id,
							'value' => NULL,
					));
				}

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

		/*
		 * Get fields
		 */
		//<editor-fold defaultstate="collapsed" desc="Get fields">
		$fields = Tbl::factory('fields')
			->select('fields.*')
			->select(array('divisions.segment', 'division_segment'))
			->select(array('divisions.name', 'division_name'))
			->join('divisions')->on('fields.division_id', '=', 'divisions.id')
			->order_by($order_column, $order_direction)
			->order_by('order', 'ASC')// なるべくorder順でソート
			->read()
			->as_array();

		foreach ($fields as $field)
		{
			$field->delete_url = URL::site("{$this->settings->backend_name}/fields/delete/{$field->id}", 'http');
		}
		// </editor-fold>

		/**
		 * If post update
		 */
		// <editor-fold defaultstate="collapsed" desc="If post update">
		if ($this->request->post('update'))
		{
			$post = $this->request->post();

			// Set to field
			foreach ($fields as $field)
			{
				$field->segment = $post['segment'][$field->id];
				$field->name = $post['name'][$field->id];
				$field->order = $post['order'][$field->id];
			}

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update
				foreach ($fields as $field)
				{
					Tbl::factory('fields')
						->get($field->id)
						->update(array(
							'segment' => $post['segment'][$field->id],
							'name' => $post['name'][$field->id],
							'order' => $post['order'][$field->id] ? : NULL,
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

		$content_file = Tpl::get_file('index', $this->settings->back_tpl_dir.'/fields', $this->partials);

		$this->content = Tpl::factory($content_file)
			->set('columns', $columns)
			->set('fields', $fields)
			->set('divisions', $divisions)
			->set('create', $create);
		// </editor-fold>

		/**
		 * Notice info
		 */
		Notice::add(Notice::WARNING, Kohana::message('general', 'relation_delete'), array(':text' => 'field'));
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

		// Get field, if there is nothing then throw to 404
		$field = Tbl::factory('fields')->get($id);
		if (!$field) throw HTTP_Exception::factory(404);

		// Database transaction start
		Database::instance()->begin();

		// Try
		try
		{
			// Get items_fields ids has this field id このfieldのidを持つitems_fieldsを取得
			$items_fields_ids = Tbl::factory('items_fields')
				->where('field_id', '=', $field->id)
				->read()
				->as_array(NULL, 'id');

			// Delete items_fields
			foreach ($items_fields_ids as $items_fields_id)
			{
				Tbl::factory('items_fields')
					->where('id', '=', $items_fields_id)
					->get()
					->delete();
			}

			// Delete field
			$field->delete();

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
		$this->redirect(URL::site("{$this->settings->backend_name}/fields/index", 'http'));
	}

	/**
	 * After
	 */
	public function after()
	{
		parent::after();
	}

}
