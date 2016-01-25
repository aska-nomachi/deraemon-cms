<?php
//error_reporting(0

$ticket = post('ticket', '');
$hostname = post('hostname', FALSE);
$database = post('database', FALSE);
$username = post('username', FALSE);
$password = post('password', FALSE);


$errors = array();

if (session('deraemon_step', 1) == 1)
{
	if ($ticket == 'stap1')
	{
		$errors = array();
		try
		{
			if (!($hostname AND $database AND $username))
			{
				$errors[] = 'すべて記入してください。';
				throw new Exception();
			}

			$dsn = 'mysql:host=' . $hostname . ';dbname=' . $database;

			$db = new PDO($dsn, $username, $password);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


			$file = './application/config/database.php';
			$code = file_get_contents($file);
			
			
			$code = str_replace(array('**1**', '**2**', '**3**', '**4**'), array($hostname, $database, $username, $password), $code);
			file_put_contents($file, $code, LOCK_EX);

			$query = file_get_contents("deraemon_cms.sql");
			$db->exec($query);

			$_SESSION['deraemon_dsn'] = $dsn;
			$_SESSION['deraemon_username'] = $username;
			$_SESSION['deraemon_password'] = $password;
			$_SESSION['deraemon_step'] = 2;
		}
		catch (PDOException $e)
		{
			$errors[] = '接続できません。';
		}
		catch (Exception $e)
		{
			
		}
	}
}

$site_url = post('site_url', FALSE);
$site_title = mb_convert_encoding(post('site_title', FALSE), 'JIS', 'SJIS');
$site_email_address = post('site_email_address', FALSE);

if (session('deraemon_step') == 2)
{
	if ($ticket == 'stap2')
	{

		try
		{
			$settings = array(
				'direct_key' => hash('sha256', rand(10000, 99999)),
				'site_title' => $site_title,
				'site_url' => $site_url,
				'site_email_address' => $site_email_address,
				'send_email_defult_admin_address' => $site_email_address,
				'author_register_activate_from_address' => $site_email_address,
				'author_register_activate_from_name' => $site_title,
				'author_register_activate_access_key' => hash('sha256', rand(10000, 99999)),
				'author_password_reset_from_address' => $site_email_address,
				'author_password_reset_from_name' => $site_title,
				'direct_key' => hash('sha256', rand(10000, 99999)),
				'encrypt_key' => hash('sha256', rand(10000, 99999)),
				'cooki_salt' => hash('sha256', rand(10000, 99999)),
				'auth_hash_key' => hash('sha256', rand(10000, 99999)),
				'auth_session_key' => hash('sha256', rand(10000, 99999)),
			);


			$dsn = session('deraemon_dsn');
			$username = session('deraemon_username');
			$password = session('deraemon_password');

			$db = new PDO($dsn, $username, $password);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			foreach ($settings as $key => $value)
			{
				$sql = "update settings set value = :value where `key` = '{$key}'";
				$stmt = $db->prepare($sql);
				$stmt->bindValue(':value', $value);
				$stmt->execute();
			}

			$_SESSION['deraemon_step'] = 'fin';
			$direct_key = $settings['direct_key'];
			$url = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
			$direct_url = "Location: http://{$url}admin/directuser?direct_key={$direct_key}";

			header($direct_url);
		}
		catch (PDOException $e)
		{
			echo $e->getMessage();

			$errors = '設定できません。';
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta charset="UTF-8" />
        <title>インストール</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

		<style>
			body
			{
				font-family: "ヒラギノ角ゴ Pro W3", "Hiragino Kaku Gothic Pro", "メイリオ", Meiryo, Osaka, "ＭＳ Ｐゴシック", "MS PGothic", sans-serif;
			}
			* {
				-webkit-box-sizing: border-box;
				-moz-box-sizing: border-box;
				box-sizing: border-box;
			}
			*:before,
			*:after {
				-webkit-box-sizing: border-box;
				-moz-box-sizing: border-box;
				box-sizing: border-box;
			}
			h1{
				margin: 80px auto 20px;
				text-align: center;
			}
			.freame{
				width: 50%;
				margin: 0 auto 0;
				padding: 10px;
				border-image-source: initial;
				border-image-slice: initial;
				border-image-width: initial;
				border-image-outset: initial;
				border-image-repeat: initial;
				border: 1px solid rgb(221, 221, 221);
				border-radius: 3px;
			}
			.freame form{
				display: block;
			}
			.freame h2{
				color: #555;
				font-weight: lighter;
				margin-bottom: 10px;
				font-size: 30px;
				line-height: 34px;
				font-weight: normal;
				border-bottom: 1px solid #333;
				padding: 0 0 0 8px;
			}
			.freame dl dt,
			.freame dl dd{
				margin: 0;
				padding: 0;
				float: left;
				margin: 0 0 10px 0;
			}
			.freame dl dt{
				width: 30%;
				clear: both;
				color: #555;
				font-size: 14px;
			}
			.freame dl dd{
				width: 70%;
			}
			.freame input{
				display: block;
				width: 100%;
				height: 24px;
				padding: 6px 12px;
				font-size: 14px;
				line-height: 1.42857143;
				color: #555;
				background-color: #fff;
				background-image: none;
				border: 1px solid #ccc;
				border-radius: 4px;
				-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
				box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
				-webkit-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
				transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
			}
			.freame .button{
				clear: both;
				text-align: right;
			}
			.freame .button button{
				display: inline-block;
				padding: 6px 12px;
				font-size: 14px;
				font-weight: normal;
				line-height: 1.4;
				text-align: center;
				text-decoration: none;
				white-space: nowrap;
				vertical-align: middle;
				cursor: pointer;
				background-image: none;
				border: 1px solid transparent;
				border-radius: 3px;
				background-color: #FFFFFF;
				border-color: #CCCCCC;
				color: #333;
			}
			.freame .button button:hover{
				background-color: #EBEBEB;
				border-color: #ADADAD;
				color: #333;
			}
		</style>
    </head>

    <body>
		<?php
		if (session('deraemon_step', 1) == 1)
		{
			?>
			<h1>DERAEMON CMS</h1>
			<div class="freame">
				<h2>インストール</h2>
				<ul>
					<?php
					if ($errors)
					{
						?>
						<h2>以下の項目を確認してください。</h2>
						<?php
						foreach ($errors as $error)
						{
							?>
							<li><?php echo $error; ?></li>
							<?php
						}
					}
					?>
				</ul>
				<form action="" method="post">
					<dl>
						<dt><label for="host">データベースサーバホ ストネーム</label></dt>
						<dd><input type="text" name="hostname" value="<?php echo $hostname; ?>" placeholder="database server hostname" /></dd>
						<dt><label for="dbname">データベースネーム</label></dt>
						<dd><input type="text" name="database" value="<?php echo $database; ?>" placeholder="database name" /></dd>
						<dt><label for="username">データベースユーザーネーム</label></dt>
						<dd><input type="text" name="username" value="<?php echo $username; ?>" placeholder="database user name" /></dd>
						<dt><label for="password">データベースパスワード</label></dt>
						<dd><input type="text" name="password" value="<?php echo $password; ?>" placeholder="database password" /></dd>
					</dl>
					<div class="button"><button type="post" name="ticket" value="stap1">送信<br />send</button></div>
				</form>
			</div>
			<?php
		}
		?>

		<?php
		if (session('deraemon_step') == 2)
		{
			?>
			<h2>設定</h2>
			<div class="freame">
				<form action="" method="post">
					<dl>
						<dt><label for="site_url">サイトURL</label></dt>
						<dd><input type="text" name="site_url" value="<?php echo $site_url; ?>" placeholder="site url" /></dd>

						<dt><label for="site_title">サイトタイトル</label></dt>
						<dd><input type="text" name="site_title" value="<?php echo $site_title; ?>" placeholder="site title" /></dd>

						<dt><label for="site_email_address">サイト メールアドレス</label></dt>
						<dd><input type="text" name="site_email_address" value="<?php echo $site_email_address; ?>" placeholder="site email address" /></dd>
					</dl>
 
					<div class="button"><button type="post" name="ticket" value="stap2">送信<br />send</button></div>
				</form>
			</div>
			<?php
		}
		?>


	</body>
</html>

<?php

function post($key, $default = NULL)
{
	return isset($_POST[$key]) ? $_POST[$key] : $default;
}

function session($key, $default = NULL)
{
	return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
}
?>
