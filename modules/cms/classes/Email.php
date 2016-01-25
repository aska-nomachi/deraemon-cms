<?php

defined('SYSPATH') or die('No direct script access.');

class Email extends Kohana_Email {

	/**
	 * kohx
	 */
	public static $_config = array();

	/**
	 * kohx::2
	 * kohxのために追加
	 * set_config
	 *
	 * 			$receive_email = Email::factory($receive_subject, $receive_message, $email->type)
	 * 				->set_config(array(
	 * 					'driver' => 'smtp',
	 * 					'options' => array(
	 * 						'hostname' => $settings->smtp_hostname,
	 * 						'username' => $settings->smtp_username,
	 * 						'password' => $settings->smtp_password,
	 * 						'port' => $settings->smtp_port,
	 * 					)
	 * 				))
	 * 				->to($email->from_address)
	 * 				->from($email->from_address, $email->from_name ? : NULL);
	 * こんな感じであとあで変更できる
	 */
	public function set_config($config)
	{
		Email::$_config = $config;

		return $this;
	}

	/**
	 * Creates a SwiftMailer instance.
	 *
	 * @return  object  Swift object
	 */
	public static function mailer()
	{
		if (!Email::$_mailer)
		{
			// kohx::3
			// kohxのために追加 -----------------------------------------------------------------------------------
			// config fileとマージ
			if (Email::$_config)
			{
				$config = Email::$_config + array(
					'driver' => 'native',
					'options' => array(),
				);
			}
			else
			{
				// Load email configuration, make sure minimum defaults are set
				$config = Kohana::$config->load('email')->as_array() + array(
					'driver' => 'native',
					'options' => array(),
				);
			}
			// ---------------------------------------------------------------------------------------------------
			// Load email configuration, make sure minimum defaults are set
			//			$config = Kohana::$config->load('email')->as_array() + array(
			//				'driver' => 'native',
			//				'options' => array(),
			//			);
			// Extract configured options
			extract($config, EXTR_SKIP);

			if ($driver === 'smtp')
			{
				// Create SMTP transport
				$transport = Swift_SmtpTransport::newInstance($options['hostname']);

				if (isset($options['port']))
				{
					// Set custom port number
					$transport->setPort($options['port']);
				}

				if (isset($options['encryption']))
				{
					// Set encryption
					$transport->setEncryption($options['encryption']);
				}

				if (isset($options['username']))
				{
					// Require authentication, username
					$transport->setUsername($options['username']);
				}

				if (isset($options['password']))
				{
					// Require authentication, password
					$transport->setPassword($options['password']);
				}

				if (isset($options['timeout']))
				{
					// Use custom timeout setting
					$transport->setTimeout($options['timeout']);
				}
			}
			elseif ($driver === 'sendmail')
			{
				// Create sendmail transport
				$transport = Swift_SendmailTransport::newInstance();

				if (isset($options['command']))
				{
					// Use custom sendmail command
					$transport->setCommand($options['command']);
				}
			}
			else
			{
				// Create native transport
				$transport = Swift_MailTransport::newInstance();

				if (isset($options['params']))
				{
					// Set extra parameters for mail()
					$transport->setExtraParams($options['params']);
				}
			}

			// Create the SwiftMailer instance
			Email::$_mailer = Swift_Mailer::newInstance($transport);
		}

		return Email::$_mailer;
	}

}
