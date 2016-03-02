<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Media extends Controller {

	private $mime = array(
		// text
		'txt' => array('type' => 'text', 'content_type' => 'text/plain'),
		'csv' => array('type' => 'text', 'content_type' => 'text/csv'),
		'css' => array('type' => 'text', 'content_type' => 'text/css'),
		'js' => array('type' => 'text', 'content_type' => 'text/javascript'),
		'html' => array('type' => 'text', 'content_type' => 'text/html'),
		// image
		'jpg' => array('type' => 'image', 'content_type' => 'image/jpeg'),
		'png' => array('type' => 'image', 'content_type' => 'image/png'),
		'gif' => array('type' => 'image', 'content_type' => 'image/gif'),
		// font Todo:: これ調べる！
		'eot' => array('type' => 'font', 'content_type' => 'font/eot'),
		'woff' => array('type' => 'font', 'content_type' => 'application/x-font-woff'),
		'ttf' => array('type' => 'font', 'content_type' => 'font/truetype'),
		'otf' => array('type' => 'font', 'content_type' => 'font/opentype'),
		'svg' => array('type' => 'font', 'content_type' => 'font/svg'),
		// video
		'mpg' => array('type' => 'video', 'content_type' => 'video/mpg'),
		'mp4' => array('type' => 'video', 'content_type' => 'video/mp4'),
		'webm' => array('type' => 'video', 'content_type' => 'video/webm'),
		'ogg' => array('type' => 'video', 'content_type' => 'video/ogg'),
	);

	/**
	 * Action index
	 *
	 *
	 * @throws HTTP_Exception
	 */
	public function action_index()
	{
		try
		{
			$dir = NULL;
			$path = NULL;
			$file = NULL;
			$ext = NULL;
			$mime = NULL;

			$staff = $this->request->param('stuff');

			$front_tpl_dir = Cms_Helper::settings('front_tpl_dir') . Cms_Helper::settings('front_theme');
			$full_path = $front_tpl_dir.'/media/'.$staff;

			// full_pathからファイルを探す
			$splited_path = explode('/', $full_path);

			foreach ($splited_path as $key => $value)
			{
				if ($key == 0)
				{
					$dir = $value;
				}
				elseif ($key == (count($splited_path) - 1))
				{
					$dotpos = strrpos($value, '.');

					if ($dotpos)
					{
						$file = substr($value, 0, $dotpos);
						$ext = substr($value, $dotpos + 1);
					}
				}
				else
				{
					$path .= $value.'/';
				}
			}

			if ($ext)
			{
				$mime = (object) $this->mime[$ext];
			}

			$filename = Kohana::find_file($dir, $path.$file, $ext);
		}
		catch (Exception $e)
		{
			throw HTTP_Exception::factory(404);
		}

		// Set render
		$rendered = FALSE;

		// If file
		if (is_file($filename))
		{
			$rendered = TRUE;

			// Calculate ETag from original file padded with the dimension specs
			$etag_sum = md5(base64_encode(file_get_contents($filename)));

			// Render as image and cache for 1 hour
			$this->response->headers('Content-Type', $mime->content_type)
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
				$this->response->body(file_get_contents($filename));
			}
		}

		// If rendered is false then throw to 404
		if (!$rendered)
		{
			throw HTTP_Exception::factory(404);
		}
	}

}
