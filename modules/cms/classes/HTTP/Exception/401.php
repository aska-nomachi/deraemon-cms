<?php

class HTTP_Exception_401 extends Kohana_HTTP_Exception_401 {

	/**
	 * Generate a Response for the 401 Exception.
	 *
	 * Unauthorized / Login Requied
	 * The user should be redirect to a login page.
	 *
	 * @return Response
	 */
	public function get_response()
	{
		// Todo:: これはどうつくるの？
		// Get tpl directory
		$home_page = Cms_Helper::settings('home_page');

		$response = Response::factory()
			->status(401)
			->headers('Location', URL::site($home_page, 'http'));

		return $response;
	}

}