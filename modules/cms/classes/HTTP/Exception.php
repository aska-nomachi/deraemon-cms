<?php

class HTTP_Exception extends Kohana_HTTP_Exception {

	/**
	 * Generate a Response for all Exceptions without a more specific override
	 *
	 * The user should see a nice error page, however, if we are in development
	 * mode we should show the normal Kohana error page.
	 *
	 * @return Response
	 */
	public function get_response()
	{
		// Lets log the Exception, Just in case it's important!
		Kohana_Exception::log($this);

		if (Kohana::$environment >= Kohana::DEVELOPMENT)
		{
			// Show the normal Kohana error page.
			return parent::get_response();
		}
		else
		{
			// Generate a nicer looking "Oops" page.
			// Get tpl directory
			$front_tpl_dir = Cms_Helper::settings('front_tpl_dir');

			// Get file
			$content_file = Tpl::get_file('default', $front_tpl_dir.'/error');

			// Set variable and render
			$content = Tpl::factory($content_file)
				->set('code', $this->getCode())
				->set('message', $this->getMessage())
				->set('request_url', URL::site(Request::current()->url(), "http"))
				->render();

			// Factory response
			$response = Response::factory();
			$response->body($content);

			return $response;
		}
	}

}