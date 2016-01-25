<?php

defined('SYSPATH') OR die('No direct script access.');

class Kohana_Tpl {

	// Array tmpname
	protected $_tmpname;
	// Array data
	protected $_data = array();
	// html string
	protected $_html = '';
	// html string
	protected $_replaced_html = '';
	// Array variables
	protected $_sign = array();
	// Array global
	protected static $_global = array();
	// temp_dir from config
	protected $_temp_dir = '';
	// temp_pre from config
	protected $_temp_pre = '';
	// tpl function class name
	protected $_tpl_func = '';

	public static function set_global($key, $value = NULL)
	{
		if (is_array($key))
		{
			foreach ($key as $key2 => $value)
			{
				Tpl::$_global[$key2] = $value;
			}
		}
		else
		{
			Tpl::$_global[$key] = $value;
		}
	}

	/**
	 * Returns a new Tpl object.
	 *
	 *     $view = Tpl::factory($html);
	 *     $view = Tpl::factory($html, array('a' => 1, 'b' => 2, 'c' => 3));
	 *
	 * @param string $html
	 * @return Tpl
	 */
	public static function factory($html = NULL, array $data = NULL)
	{
		return new Tpl($html, $data);
	}

	/**
	 * constractar
	 *
	 */
	public function __construct($html, array $data = NULL)
	{
		$this->_html = $html;

		if ($data !== NULL)
		{
			// Add the values to the current data
			$this->_data = $data + $this->_data;
		}

		$this->_temp_dir = Kohana::$config->load('tpl')->get('temp_dir');
		$this->_temp_pre = Kohana::$config->load('tpl')->get('temp_pre');
		$this->_tpl_func = Kohana::$config->load('tpl')->get('tpl_func');

		$this->sign();
	}

	/**
	 * Assigns a variable by name.
	 *
	 *     // This value can be accessed as $foo within the view
	 *     $view->set('foo', 'my value');
	 *
	 * You can also use an array to set several values at once:
	 *
	 *     // Create the values $food and $beverage in the view
	 *     $view->set(array('food' => 'bread', 'beverage' => 'water'));
	 *
	 * @param   string  $key    variable name or an array of variables
	 * @param   mixed   $value  value
	 * @return  $this
	 */
	public function set($key, $value = NULL)
	{
		if (is_array($key))
		{
			foreach ($key as $name => $value)
			{
				$this->_data[$name] = $value;
			}
		}
		else
		{
			$this->_data[$key] = $value;
		}

		return $this;
	}

	/**
	 * func {{~func()}}
	 */
	// ファンクションを呼び出して、パラメータを配列にして送る。 number、string、arrayはOK！
	private function sign_func($key, $string)
	{
		// 頭の文字「~」を取り除く
		$string = substr($string, 1);

		// もし「=」があれば分ける、「array(''=>'')」「is('aaa', '=', 'aaa')」避けるため、次の文字は「>」「=」以外の時
		$equal_pos = strpos($string, '=');
		$equal_next = substr($string, $equal_pos + 1, 1);
		if ($equal_pos !== FALSE AND $equal_next !== '>' AND $equal_next !== '=')
		{
			$assign = trim(substr($string, 0, $equal_pos));
			$method_param = trim(substr($string, $equal_pos + 1));
		}
		else
		{
			$assign = NULL;
			$method_param = trim($string);
		}

		// メソッドとパラメータを分ける 最初の「(」で分ける
		$brace_pos = strpos($method_param, '(');
		$method = substr($method_param, 0, $brace_pos);
		$param_string = substr($method_param, $brace_pos + 1, -1);

		/*
		 * パラメータを作る！
		 */
		// 命令文の作成
		$this->_sign[$key] = '<?php ';

		// param_fixed_stringを作る
		$param_fixed_strings = array();

		// param_stirngを「,」でとりあえず分ける
		$param_strings = explode(',', $param_string);

		// param_stringsをイテレート
		foreach ($param_strings as $param_string)
		{
			// param_stringを一時的に修正
			// php5.3だから「[]」も！
			$param_temp_string = NULL;

			$double_arrow = strpos($param_string, '=>');

			if ($double_arrow !== FALSE)
			{
				$param_temp_string = str_replace(array('array(', ')', '[', ']'), '', trim(substr($param_string, $double_arrow + 2)));
			}
			else
			{
				$param_temp_string = str_replace(array('array(', ')', '[', ']'), '', trim($param_string));
			}

			// もし「'」を含んでいたら文字列、
			// is_numericがtrueなら数字、
			// 「true」、「false」、「null」はそのまま、それ以外はvariable
			if (
				strpos($param_temp_string, '\'') === FALSE
				AND strpos($param_temp_string, '=>') === FALSE
				AND ! is_numeric($param_temp_string)
				AND strtolower($param_temp_string) !== 'true'
				AND strtolower($param_temp_string) !== 'false'
				AND strtolower($param_temp_string) !== 'null'
				AND strtolower($param_temp_string) !== ''
			)
			{
				$fragments = explode('.', $param_temp_string);
				$total = count($fragments);

				if ($total == 1)
				{
					$variable = $fragments[0];

					$this->_sign[$key] .= '$'.$variable.' = (isset($'.$variable.')) ? $'.$variable.' : NULL;';

					// param fixed stringsにセット
					$param_fixed_strings[] = str_replace($param_temp_string, '$'.$variable, $param_string);
				}
				else
				{
					$count = 1;
					foreach ($fragments as $fragment)
					{
						if ($count == 1)
						{
							$variable = $fragment;
						}
						else if ($count == $total)
						{
							$variable .= '[\''.$fragment.'\']';
						}
						else
						{
							$variable .= '[\''.$fragment.'\']';
						}

						$this->_sign[$key] .= 'if(isset($'.$variable.')){';
						$this->_sign[$key] .= 'if(is_object($'.$variable.')) $'.$variable.' = (array) $'.$variable.';';
						$this->_sign[$key] .= '}';
						$this->_sign[$key] .= 'else{';
						$this->_sign[$key] .= '$'.$variable.' = NULL;';
						$this->_sign[$key] .= '}';

						$count++;
					}

					// param fixed stringsにセット
					$param_fixed_strings[] = str_replace($param_temp_string, '$'.$variable, $param_string);
				}
			}
			else
			{
				// param fixed stringsにセット
				$param_fixed_strings[] = $param_string;
			}
		}

		// functionの組み立て
		$function_string = $this->_tpl_func.'::'.trim($method).'('.implode(',', $param_fixed_strings).');';

		// もし$assignがある場合
		if ($assign)
		{
			$this->_sign[$key] .= '$'.$assign.' = '.$function_string;
		}
		// ないときはストリング可チェックしてストリングなら表示そうでない時はarray!
		else
		{
			// debugの時はそのまま出す
			if ($method === 'debug' OR $method === 'test')
			{
				$this->_sign[$key] .= '$function_return = '.$function_string.' echo $function_return;';
			}
			else
			{
				// Todo:: int、string、== NULL、bool以外は「array！」って出す。
				$this->_sign[$key] .= '$function_return = '.$function_string.' if(is_string($function_return) OR is_int($function_return)) { echo $function_return; } elseif(is_bool($function_return)) { echo (bool) $function_return;} elseif(is_array($function_return) AND $function_return != NULL) { echo \'array!\';}';
			}
		}

		$this->_sign[$key] .= ' ?>';
	}

	/**
	 * call {{%items()}}
	 */
	// 文字列でファンクションを呼び出しす。「param1 = [1,2,3], param2 = ['aaa','bbb']」の形
	// 「[]」の中のネストはできない
	// %が使える関数はパラメータは「１つの配列」にする。
	private function sign_call($key, $string)
	{
		// 頭の文字「%」を取り除く
		$string = substr($string, 1);

		// アサイン＋メソッドとパラメータを分ける 最初の「(」で分ける
		$brace_pos = strpos($string, '(');
		$assign_method = substr($string, 0, $brace_pos);
		$param_string = substr($string, $brace_pos + 1, -1);

		// もし「=」があれば分ける
		$equal_pos = strpos($assign_method, '=');
		if ($equal_pos !== FALSE)
		{
			$assign = trim(substr($assign_method, 0, $equal_pos));
			$method = trim(substr($assign_method, $equal_pos + 1));
		}
		else
		{
			$assign = NULL;
			$method = trim($assign_method);
		}

		/*
		 * パラメータを作る！
		 */

		// 命令文の作成
		$this->_sign[$key] = '<?php ';

		// param_fixed_stringを作る
		$param_fixed_string = '[';

		$param_strings = explode(',', str_replace('=', '=,', $param_string));

		foreach ($param_strings as $param_string)
		{
			$param_temp_string = trim($param_string);

			if (strpos($param_temp_string, '=') !== FALSE)
			{
				$param_fixed_string .= '\''.trim(str_replace('=', '', $param_temp_string)).'\''.' => ';
			}
			// もし「'」を含んでいたら文字列、is_numericがtrueなら数字なのでそれ以外はvariableなのでここに入る
			else if (
				strpos($param_temp_string, '\'') === FALSE
				AND ! is_numeric($param_temp_string)
				AND strtolower($param_temp_string) !== 'true'
				AND strtolower($param_temp_string) !== 'false'
				AND strtolower($param_temp_string) !== 'null'
			)
			{
				$fragments = explode('.', $param_string);
				$total = count($fragments);

				if ($total == 1)
				{
					$variable = trim($fragments[0]);

					$this->_sign[$key] .= '$'.$variable.' = (isset($'.$variable.')) ? $'.$variable.' : NULL;';

					// param value stringにセット
					$param_fixed_string .= str_replace($variable, '$'.$variable, $param_string).',';
				}
				else
				{
					// '['と']'を確保してあとで付ける
					$variable_start = strpos($param_temp_string, '[') !== FALSE ? '[' : '';
					$variable_end = strpos($param_temp_string, ']') !== FALSE ? ']' : '';

					$count = 1;
					foreach ($fragments as $fragment)
					{
						$fragment = trim(str_replace(array('[', ']'), '', $fragment));
						if ($count == 1)
						{
							$variable = $fragment;
						}
						else if ($count == $total)
						{
							$variable .= '[\''.$fragment.'\']';
						}
						else
						{
							$variable .= '[\''.$fragment.'\']';
						}

						$this->_sign[$key] .= 'if(isset($'.$variable.')){';
						$this->_sign[$key] .= 'if(is_object($'.$variable.')) $'.$variable.' = (array) $'.$variable.';';
						$this->_sign[$key] .= '}';
						$this->_sign[$key] .= 'else{';
						$this->_sign[$key] .= '$'.$variable.' = NULL;';
						$this->_sign[$key] .= '}';
						$count++;
					}
					// 確保した'['と']'をつけてparam value stringにセット
					$param_fixed_string .= $variable_start.str_replace($param_string, '$'.$variable, $param_string).$variable_end.',';
				}
			}
			else
			{
				// param value stringにセット
				$param_fixed_string .= $param_string.',';
			}
		}

		$param_fixed_string .= ']';

		// もし$assignがある場合
		if ($assign)
		{
			$this->_sign[$key] .= '$'.$assign.' = '.$this->_tpl_func.'::'.$method.'('.$param_fixed_string.'); ?>';
		}
		else
		{
			$this->_sign[$key] .= 'echo '.$this->_tpl_func.'::'.$method.'('.$param_fixed_string.'); ?>';
		}
	}

	/**
	 * if {{#items}} or {{^items}}
	 */
	private function sign_if($key, $string, $not = FALSE)
	{
		$strings = explode(':', substr($string, 1));
		$fragments = explode('.', str_replace(' ', '', $strings[0]));

		$variable = $fragments[0];
		$value = isset($strings[1]) ? trim($strings[1]) : NULL;

		$this->_sign[$key] = (!$not) ? '<?php if (isset($'.$variable.')) : ?>' : '';
		$this->_sign[$key] .= '<?php if (is_object($'.$variable.')) $'.$variable.' = (array) $'.$variable.';';

		for ($i = 1; $i < count($fragments); $i++)
		{
			$variable .= '[\''.$fragments[$i].'\']';
			$this->_sign[$key] .= ' if (isset($'.$variable.') AND is_object($'.$variable.')) $'.$variable.' = (array) $'.$variable.';';
		}

		$this->_sign[$key] .= (!$not) ? ' if (isset($'.$variable.') AND $'.$variable.') :' : ' if ( ! isset($'.$variable.') OR ! $'.$variable.') :';

		if ($value)
		{
			$this->_sign[$key] .= '$'.$value.' = $'.$variable.';';
		}

		$this->_sign[$key] .= ' ?>';
	}

	/**
	 * foreach {{*items:item}}
	 */
	private function sign_foreach($key, $string)
	{
		$strings = explode(':', substr($string, 1));
		$fragments = explode('.', str_replace(' ', '', $strings[0]));

		$variable = $fragments[0];
		$value = trim($strings[1]);

		$this->_sign[$key] = '<?php if (isset($'.$variable.')) : ?>';
		$this->_sign[$key] .= '<?php if (is_object($'.$variable.')) $'.$variable.' = (array) $'.$variable.';';

		for ($i = 1; $i < count($fragments); $i++)
		{
			$variable .= '[\''.$fragments[$i].'\']';
			$this->_sign[$key] .= ' if (isset($'.$variable.') AND is_object($'.$variable.')) $'.$variable.' = (array) $'.$variable.';';
		}

		$this->_sign[$key] .= ' foreach ($'.$variable.' as $'.$value.') : ?>';
	}

	/**
	 * end {{/#}} {{/^}} {{/*}}
	 */
	private function sign_end($key, $string)
	{
		$marker = substr($string, 1);

		switch ($marker)
		{
			case '#':
				{
					$this->_sign[$key] = '<?php endif; ?><?php endif; ?>';
					break;
				}
			case '^':
				{
					$this->_sign[$key] = '<?php endif; ?>';
					break;
				}
			case '%':
				{
					$this->_sign[$key] = '<?php endfor; ?>';
					break;
				}
			case '*':
				{
					$this->_sign[$key] = '<?php endforeach; ?><?php endif; ?>';
					break;
				}
			default:
				{
					break;
				}
		}
	}

	/**
	 * Ignore {{!......}}
	 */
	private function sign_ignore($key)
	{
		$this->_sign[$key] = '';
	}

	/**
	 * php {{? echo $value; }}
	 */
	private function sign_php($key, $string)
	{
		$statement = trim(substr($string, 1));
		$this->_sign[$key] = '<?php '.$statement.' ?>';
	}

	/**
	 * {{items}} or {{&items}}
	 */
	private function sign_echo($key, $string, $html_chars = TRUE)
	{
		$string = str_replace(' ', '', $string);
		$fragments = $html_chars ? explode('.', $string) : explode('.', substr($string, 1));
		$count = 1;
		$total = count($fragments);

		$this->_sign[$key] = '<?php ';

		if ($total == 1)
		{
			$variable = $fragments[0];
		}
		else
		{
			foreach ($fragments as $fragment)
			{
				if ($count == 1)
				{
					$variable = $fragment;
					$this->_sign[$key] .= 'if (isset($'.$variable.') AND is_object($'.$variable.')) $'.$variable.' = (array) $'.$variable.';';
				}
				elseif ($count == $total)
				{
					$variable .= '[\''.$fragment.'\']';
				}
				else
				{
					$variable .= '[\''.$fragment.'\']';
					$this->_sign[$key] .= ' ';
					$this->_sign[$key] .= 'if (isset($'.$variable.') AND is_object($'.$variable.')) $'.$variable.' = (array) $'.$variable.';';
				}

				$count++;
			}
		}

		$this->_sign[$key] .= 'if(isset($'.$variable.')){';
		$this->_sign[$key] .= 'if(is_string($'.$variable.') OR is_int($'.$variable.')){';
		$this->_sign[$key] .= $html_chars ? 'echo HTML::chars($'.$variable.');' : 'echo $'.$variable.';';
		$this->_sign[$key] .= '} elseif(is_bool($'.$variable.')) {echo (bool) $'.$variable.';} elseif(is_array($'.$variable.') AND $'.$variable.' != NULL) {echo \'array!\';}} ?>'; // Todo:: int、string、== NULL、bool以外は「array！」って出す。
	}

	/**
	 * partial{{>partial}}
	 */
	private function sign_partial($key)
	{
		$this->_sign[$key] = '';
	}

	/**
	 * sign()
	 */
	private function sign()
	{
		$tpl_sign = Profiler::start('tpl', 'sign');

		// Seaarch Todo:: __sdfsdf__ とか ``adsf``,((asdf)), @@asdf@@, ~~asdf~~, --asdf--
		preg_match_all("/{{(.[^{}]*)}}/", $this->_html, $matches, PREG_SET_ORDER);

		// Build
		foreach ($matches as $matche)
		{
			$key = $matche[0];
			$string = trim($matche[1]);
			$prefix = substr($string, 0, 1);

			switch ($prefix)
			{
				case '~':
					$this->sign_func($key, $string);
					break;

				case '%':
					$this->sign_call($key, $string);
					break;

				case '#':
					$this->sign_if($key, $string);
					break;

				case '^':
					$this->sign_if($key, $string, TRUE);
					break;

				case '*':
					$this->sign_foreach($key, $string);
					break;

				case '/':
					$this->sign_end($key, $string);
					break;

				case '!':
					$this->sign_ignore($key);
					break;

				case '?':
					$this->sign_php($key, $string);
					break;

				case '&':
					$this->sign_echo($key, $string, FALSE);
					break;

				case '>':
					$this->sign_partial($key);
					break;

				default:
					$this->sign_echo($key, $string);
					break;
			}
		}

		Profiler::stop($tpl_sign);
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function render()
	{
		
		$tpl_render = Profiler::start('tpl', 'render');

		
		
		
		// Build replaced html
		$this->_replaced_html = str_replace(array_keys($this->_sign), array_values($this->_sign), $this->_html);
//		echo Debug::vars($this->_replaced_html);
		
		
		// get temp path
		$temp_dir_path = DOCROOT.'application/'.$this->_temp_dir;

		// Check dir and create
		if (!(file_exists($temp_dir_path) && is_dir($temp_dir_path)))
		{
			mkdir($temp_dir_path);
		}

		// Output buffering
		$this->_tmpname = tempnam($temp_dir_path, $this->_temp_pre);

		file_put_contents($this->_tmpname, $this->_replaced_html);

		// Import the Tpl variables to local namespace
		extract($this->_data);

		if (Tpl::$_global)
		{
			// Import the global Tpl variables to local namespace
			extract(Tpl::$_global);
		}

		ob_start();
		ob_implicit_flush(0);

		require $this->_tmpname;

		$html = ob_get_clean();

		// Remove tempfile
		if (is_dir($temp_dir_path))
		{
			$files = scandir($temp_dir_path);

			foreach ($files as $file)
			{
				if (!is_dir($file))
				{
					unlink($temp_dir_path.'/'.$file);
				}
			}
		}

		Profiler::stop($tpl_render);
		return $html;
	}

	/**
	 * Get file content
	 *
	 * $partials = array(
	 * 	'header' => '<header>header content</header>',
	 * 	'footer' => '<footer>footer content</footer>',
	 * );
	 *
	 * replace {{>header}} inside file content.
	 *
	 * @param string  $file
	 * @param array   $partial
	 * @param string  $path
	 * @param string  $ext
	 * @param boolean $array
	 * @return string
	 */
	public static function get_file($file, $path = NULL, $partial = NULL, $ext = 'html')
	{
		// Get file content
		$file_path = Kohana::find_file($path, $file, $ext);

		if ($file_path)
		{
			$content = file_get_contents($file_path);

			// If there are partials
			if ($partial)
			{
				$partial_keys = array_keys($partial);

				foreach ($partial_keys as &$partial_key)
				{
					$partial_key = '{{>'.$partial_key.'}}';
				}

				$content = str_replace($partial_keys, array_values($partial), $content);
			}
		}
		else
		{
			$content = FALSE;
		}

		return $content;
	}

}
