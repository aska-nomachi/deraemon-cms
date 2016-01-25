<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Imagefly extends Controller {

	/**
	 * Action index
	 *
	 * Example,  imagefly/1/w253-h253-h/test4.jpg
	 * 					  imagefly/1/w-h-h/test4.jpg
	 *
	 * direction, portrait/landscape/square/original
	 *
	 * @throws HTTP_Exception
	 */
	public function action_index()
	{
		// Get param
		try
		{
			$staff = $this->request->param('stuff');
			$stuffs = explode('/', $staff);
			$num = count($stuffs);
			$paths = array_slice($stuffs, 0, $num - 2);

			list($width_string, $height_string, $direction) = explode('-', $stuffs[$num - 2]);

			list($segment, $ext) = explode('.', $stuffs[$num - 1]);

			$width = substr($width_string, 1) ? substr($width_string, 1) : 0;

			$height = substr($height_string, 1) ? substr($height_string, 1) : 0;

			// Get content type
			switch ($ext)
			{
				case 'jpg':
					$content_type = 'image/jpeg';
					break;

				case 'png':
					$content_type = 'image/png';
					break;

				case 'gif':
					$content_type = 'image/gif';
					break;

				default:
					$content_type = NULL;
					break;
			}

			$first_dir = reset($paths);
			if (!in_array($first_dir, array('item', 'user')))
			{
				throw HTTP_Exception::factory(404);
			}

			$image_dir = Cms_Helper::settings('image_dir');

			$dir = $image_dir.'/'.implode('/', $paths);

			$file = $segment;

			if ($direction !== 'o')
			{
				$file .= '_'.$direction;
			}
			$filename = Kohana::find_file($dir, $file, $ext);
		}
		catch (ErrorException $e)
		{
			throw HTTP_Exception::factory(404);
		}

		// Set render
		$rendered = FALSE;

		// If file
		if (is_file($filename))
		{
			// Render image
			$this->_render_image($filename, $ext, $width, $height, $content_type);
			$rendered = TRUE;
		}

		// If rendered is false then throw to 404
		if (!$rendered)
		{
			throw HTTP_Exception::factory(404);
		}
	}

	/**
	 * Render image
	 *
	 * @param string $filename
	 * @param string $ext
	 * @param string $width
	 * @param string $height
	 * @param string $content_type
	 */
	protected function _render_image($filename, $ext, $width, $height, $content_type)
	{
		// Calculate ETag from original file padded with the dimension specs
		$etag_sum = md5(base64_encode(file_get_contents($filename)).$width.','.$height);

		// Render as image and cache for 1 hour
		$this->response->headers('Content-Type', $content_type)
			->headers('Cache-Control', 'max-age='.Date::HOUR.', public, must-revalidate')
			->headers('Expires', gmdate('D, d M Y H:i:s', time() + Date::HOUR).' GMT')
			->headers('Last-Modified', date('r', filemtime($filename)))
			->headers('ETag', $etag_sum);

		if ($this->request->headers('if-none-match') AND (string) $this->request->headers('if-none-match') === $etag_sum)
		{
			$this->response->status(304)
				->headers('Content-Length', '0');
		}
		else
		{
			$result = Image::factory($filename);

			if ($width OR $height)
			{
				$result->resize($width, $height);
			}

			$result->render($ext);

			$this->response->body($result);
		}
	}

}
