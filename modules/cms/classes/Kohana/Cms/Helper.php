<?php

defined('SYSPATH') OR die('No direct script access.');

class Kohana_Cms_Helper {

	/**
	 * get settings
	 *
	 * @param string $key setting key
	 * @uses Tbl module
	 * @return string Value
	 */
	public static function settings($key = NULL)
	{
		$settings = (object) Tbl::factory('settings')
				->read()
				->as_array('key', 'value');

		return $key ? $settings->{$key} : $settings;
	}

	/**
	 * sec
	 *
	 * Cms_Helper::sec('2week'); year, month, week, day, hour, minute, 2week, 3week, 2day, 3day ...
	 *
	 * @param string $string
	 * @return int seconds
	 */
	public static function sec($string)
	{
		preg_match('/[0-9]+/', $string, $num_array);

		$num = (isset($num_array[0])) ? $num_array[0] : NULL;

		$str = ($num) ? str_replace($num, '', $string) : $string;

		switch ($str)
		{
			case 'year':
				$expiration = 31556926;
				break;
			case 'month':
				$expiration = 2629744;
				break;
			case 'week':
				$expiration = 604800;
				break;
			case 'day':
				$expiration = 86400;
				break;
			case 'hour':
				$expiration = 3600;
				break;
			case 'minute':
				$expiration = 60;
				break;
			default:
				$expiration = NULL;
				break;
		}

		if ($num AND $str)
		{
			$return = $num * $expiration;
		}
		if ($num AND !$str)
		{
			$return = $num;
		}
		else
		{
			$return = $expiration;
		}

		return $return;
	}

	/**
	 * Make directory
	 *
	 * @param string $dirname directory name
	 * @param string $path directory path
	 * @return bool
	 */
	public static function make_dir($dirname, $path = NULL)
	{
		$return = FALSE;

		if ($path)
		{
			if (substr($path, -1, 1) !== '/')
			{
				$path = $path.'/';
			}
		}

		$dir = 'application/'.$path.$dirname;

		if (!(file_exists($dir) && is_dir($dir)))
		{
			$return = mkdir($dir);
		}

		return $return;
	}

	/**
	 * Rename directory
	 *
	 * @param string $oldname directory old name
	 * @param string $newname directory new name
	 * @param string $path directory path
	 * @return bool
	 */
	public static function rename_dir($oldname, $newname, $path = NULL)
	{
		$return = FALSE;

		if ($path)
		{
			if (substr($path, -1, 1) !== '/')
			{
				$path = 'application/'.$path.'/';
			}
			else
			{
				$path = 'application/'.$path;
			}
		}

		$old = $path.$oldname;
		$new = $path.$newname;

		if (file_exists($old) && is_dir($old))
		{
			$return = rename($old, $new);
		}

		return $return;
	}

	/**
	 * Delete directory
	 *
	 * @param string $dirname directory name
	 * @param $path $newname directory path
	 * @param $with_fiels delete with files
	 * @return bool
	 */
	public static function delete_dir($dirname, $path = NULL, $with_fiels = FALSE)
	{
		$return = NULL;

		if ($path)
		{
			if (substr($path, -1, 1) !== '/')
			{
				$path = 'application/'.$path.'/';
			}
			else
			{
				$path = 'application/'.$path;
			}
		}

		$dir = $path.$dirname;

		if (file_exists($dir) && is_dir($dir))
		{
			if ($with_fiels)
			{
				$files = scandir($dir);

				foreach ($files as $file)
				{
					if (!is_dir($file))
					{
						$return = unlink($dir.'/'.$file);
					}
				}
			}

			$return = rmdir($dir);

			return $return;
		}
	}

	/**
	 * Set file
	 *
	 * Tpl::set_file('wrapper/html', 'tpls', '<p>content</p>', 'php');
	 *
	 * @param string $filename file neme
	 * @param string $path directory path
	 * @param string $content file content
	 * @param string $ext file extension
	 * @return int This function returns the number of bytes that were written to the file, or <b>FALSE</b> on failure.
	 */
	public static function set_file($filename, $path = NULL, $content = NULL, $ext = 'html')
	{
		if ($path)
		{
			if (substr($path, -1, 1) !== '/')
			{
				$path = 'application/'.$path.'/';
			}
			else
			{
				$path = 'application/'.$path;
			}
		}

		// Check dir and create
		if (!(file_exists($path) && is_dir($path)))
		{
			mkdir(substr($path, 0, -1), 0777, TRUE);
		}

		$file_path = "{$path}{$filename}.{$ext}";

		return file_put_contents($file_path, $content);
	}

	/**
	 * Get dirfiles
	 *
	 * @param string $dirname directory name
	 * @param string $path directory path
	 * @param string $ext file extension
	 * @return array filenames
	 */
	public static function get_dirfiles($dirname, $path = NULL, $ext = 'html')
	{
		$return = array();

		if ($path)
		{
			if (substr($path, -1, 1) !== '/')
			{
				$path = 'application/'.$path.'/';
			}
			else
			{
				$path = 'application/'.$path;
			}
		}

		$dir = $path.$dirname;

		if (file_exists($dir) && is_dir($dir))
		{
			$files = scandir($dir);
			foreach ($files as $file)
			{
				$segment = basename($file, ".{$ext}");
				
				if (!is_dir($file))
				{
					$return[$segment] = (object) array(
						'segment' => $segment,
					);
				}
			}
		}

		return (object) $return;
	}

	/**
	 * Rename file
	 *
	 * Tpl::rename_file('wrapper/html', 'wrapper/new_html', 'tpls', 'php')
	 *
	 * @param string $oldname old file neme
	 * @param string $newname new file neme
	 * @param string $path directory path
	 * @param string $ext file extension
	 * @return bool
	 */
	public static function rename_file($oldname, $newname, $path = NULL, $ext = 'html')
	{
		$return = NULL;

		if ($path)
		{
			if (substr($path, -1, 1) !== '/')
			{
				$path = 'application/'.$path.'/';
			}
			else
			{
				$path = 'application/'.$path;
			}
		}

		$old = "{$path}{$oldname}.{$ext}";
		$new = "{$path}{$newname}.{$ext}";

		if (file_exists($old) && is_file($old))
		{
			$return = rename($old, $new);
		}

		return $return;
	}

	/**
	 * Delete file
	 *
	 * Tpl::delete_file('wrapper/html', 'tpls', 'php')
	 *
	 * @param string $filename file neme
	 * @param string $path directory path
	 * @param string $ext file extension
	 * @return bool
	 */
	public static function delete_file($filename, $path = NULL, $ext = 'html')
	{
		$return = NULL;

		if ($path)
		{
			if (substr($path, -1, 1) !== '/')
			{
				$path = 'application/'.$path.'/';
			}
			else
			{
				$path = 'application/'.$path;
			}
		}

		$file_path = "{$path}{$filename}.{$ext}";

		if (file_exists($file_path) && is_file($file_path))
		{
			$return = unlink($file_path);
		}

		return $return;
	}

}
