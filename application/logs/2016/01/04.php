<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2016-01-04 08:50:26 --- CRITICAL: Database_Exception [ 2 ]: mysql_connect():  ~ MODPATH\database\classes\Kohana\Database\MySQL.php [ 67 ] in C:\wamp\www\deraemon-cms_0.8.7\modules\database\classes\Kohana\Database\MySQL.php:171
2016-01-04 08:50:26 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\modules\database\classes\Kohana\Database\MySQL.php(171): Kohana_Database_MySQL->connect()
#1 C:\wamp\www\deraemon-cms_0.8.7\modules\database\classes\Kohana\Database\Query.php(251): Kohana_Database_MySQL->query(1, 'SELECT * FROM `...', true, Array)
#2 C:\wamp\www\deraemon-cms_0.8.7\modules\tbl\classes\Kohana\Tbl.php(294): Kohana_Database_Query->execute()
#3 C:\wamp\www\deraemon-cms_0.8.7\application\bootstrap.php(136): Kohana_Tbl->read()
#4 C:\wamp\www\deraemon-cms_0.8.7\index.php(114): require('C:\\wamp\\www\\der...')
#5 {main} in C:\wamp\www\deraemon-cms_0.8.7\modules\database\classes\Kohana\Database\MySQL.php:171
2016-01-04 08:51:58 --- CRITICAL: Database_Exception [ 2 ]: mysql_connect():  ~ MODPATH\database\classes\Kohana\Database\MySQL.php [ 67 ] in C:\wamp\www\deraemon-cms_0.8.7\modules\database\classes\Kohana\Database\MySQL.php:171
2016-01-04 08:51:58 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\modules\database\classes\Kohana\Database\MySQL.php(171): Kohana_Database_MySQL->connect()
#1 C:\wamp\www\deraemon-cms_0.8.7\modules\database\classes\Kohana\Database\Query.php(251): Kohana_Database_MySQL->query(1, 'SELECT * FROM `...', true, Array)
#2 C:\wamp\www\deraemon-cms_0.8.7\modules\tbl\classes\Kohana\Tbl.php(294): Kohana_Database_Query->execute()
#3 C:\wamp\www\deraemon-cms_0.8.7\application\bootstrap.php(136): Kohana_Tbl->read()
#4 C:\wamp\www\deraemon-cms_0.8.7\index.php(114): require('C:\\wamp\\www\\der...')
#5 {main} in C:\wamp\www\deraemon-cms_0.8.7\modules\database\classes\Kohana\Database\MySQL.php:171
2016-01-04 08:54:54 --- CRITICAL: Database_Exception [ 1146 ]: Table 'default_emon.settings' doesn't exist [ SELECT * FROM `settings` ] ~ MODPATH\database\classes\Kohana\Database\MySQL.php [ 194 ] in C:\wamp\www\deraemon-cms_0.8.7\modules\database\classes\Kohana\Database\Query.php:251
2016-01-04 08:54:54 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\modules\database\classes\Kohana\Database\Query.php(251): Kohana_Database_MySQL->query(1, 'SELECT * FROM `...', true, Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\modules\tbl\classes\Kohana\Tbl.php(294): Kohana_Database_Query->execute()
#2 C:\wamp\www\deraemon-cms_0.8.7\application\bootstrap.php(136): Kohana_Tbl->read()
#3 C:\wamp\www\deraemon-cms_0.8.7\index.php(114): require('C:\\wamp\\www\\der...')
#4 {main} in C:\wamp\www\deraemon-cms_0.8.7\modules\database\classes\Kohana\Database\Query.php:251
2016-01-04 08:57:27 --- CRITICAL: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: deraemon-cms_0.8.7 ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 08:57:27 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(975): Kohana_HTTP_Exception::factory(404, 'Unable to find ...', Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#2 {main} in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 08:57:33 --- CRITICAL: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: deraemon-cms_0.8.7/index.php ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 08:57:33 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(975): Kohana_HTTP_Exception::factory(404, 'Unable to find ...', Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#2 {main} in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 08:57:33 --- CRITICAL: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: deraemon-cms_0.8.7/index.php ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 08:57:33 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(975): Kohana_HTTP_Exception::factory(404, 'Unable to find ...', Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#2 {main} in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 08:57:33 --- CRITICAL: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: deraemon-cms_0.8.7/index.php ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 08:57:33 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(975): Kohana_HTTP_Exception::factory(404, 'Unable to find ...', Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#2 {main} in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 08:57:33 --- CRITICAL: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: deraemon-cms_0.8.7/index.php ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 08:57:33 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(975): Kohana_HTTP_Exception::factory(404, 'Unable to find ...', Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#2 {main} in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 08:57:33 --- CRITICAL: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: deraemon-cms_0.8.7/index.php ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 08:57:33 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(975): Kohana_HTTP_Exception::factory(404, 'Unable to find ...', Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#2 {main} in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 08:57:33 --- CRITICAL: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: deraemon-cms_0.8.7/index.php ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 08:57:33 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(975): Kohana_HTTP_Exception::factory(404, 'Unable to find ...', Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#2 {main} in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 08:57:38 --- CRITICAL: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: deraemon-cms_0.8.7 ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 08:57:38 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(975): Kohana_HTTP_Exception::factory(404, 'Unable to find ...', Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#2 {main} in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 08:58:37 --- CRITICAL: HTTP_Exception_404 [ 404 ]: The requested URL / was not found on this server. ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php:79
2016-01-04 08:58:37 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(79): Kohana_HTTP_Exception::factory(404, 'The requested U...', Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#2 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#3 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#4 {main} in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php:79
2016-01-04 08:58:39 --- CRITICAL: HTTP_Exception_404 [ 404 ]: The requested URL / was not found on this server. ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php:79
2016-01-04 08:58:39 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(79): Kohana_HTTP_Exception::factory(404, 'The requested U...', Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#2 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#3 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#4 {main} in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php:79
2016-01-04 08:58:39 --- CRITICAL: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: index.php ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 08:58:39 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(975): Kohana_HTTP_Exception::factory(404, 'Unable to find ...', Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#2 {main} in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 08:58:39 --- CRITICAL: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: index.php ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 08:58:39 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(975): Kohana_HTTP_Exception::factory(404, 'Unable to find ...', Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#2 {main} in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 08:58:39 --- CRITICAL: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: index.php ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 08:58:39 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(975): Kohana_HTTP_Exception::factory(404, 'Unable to find ...', Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#2 {main} in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 08:58:39 --- CRITICAL: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: index.php ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 08:58:39 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(975): Kohana_HTTP_Exception::factory(404, 'Unable to find ...', Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#2 {main} in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 08:58:39 --- CRITICAL: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: index.php ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 08:58:39 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(975): Kohana_HTTP_Exception::factory(404, 'Unable to find ...', Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#2 {main} in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 08:58:41 --- CRITICAL: HTTP_Exception_404 [ 404 ]: The requested URL / was not found on this server. ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php:79
2016-01-04 08:58:41 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(79): Kohana_HTTP_Exception::factory(404, 'The requested U...', Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#2 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#3 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#4 {main} in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php:79
2016-01-04 09:09:03 --- CRITICAL: ErrorException [ 2 ]: unlink(C:\wamp\www\deraemon-cms_0.8.7\application/contents/temp/tem1E84.tmp): Permission denied ~ MODPATH\tpl\classes\Kohana\Tpl.php [ 660 ] in :
2016-01-04 09:09:03 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unlink(C:\\wamp\\...', 'C:\\wamp\\www\\der...', 660, Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\modules\tpl\classes\Kohana\Tpl.php(660): unlink('C:\\wamp\\www\\der...')
#2 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Template.php(526): Kohana_Tpl->render()
#3 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(87): Controller_Backend_Template->after()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Backend_Auth))
#6 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#8 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#9 {main} in :
2016-01-04 09:37:05 --- CRITICAL: ErrorException [ 2 ]: unlink(C:\wamp\www\deraemon-cms_0.8.7\application/contents/temp/temC93C.tmp): Permission denied ~ MODPATH\tpl\classes\Kohana\Tpl.php [ 660 ] in :
2016-01-04 09:37:05 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unlink(C:\\wamp\\...', 'C:\\wamp\\www\\der...', 660, Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\modules\tpl\classes\Kohana\Tpl.php(660): unlink('C:\\wamp\\www\\der...')
#2 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Template.php(526): Kohana_Tpl->render()
#3 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Home.php(93): Controller_Backend_Template->after()
#4 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(87): Controller_Backend_Home->after()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Backend_Home))
#7 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#10 {main} in :
2016-01-04 09:37:09 --- CRITICAL: ErrorException [ 2 ]: unlink(C:\wamp\www\deraemon-cms_0.8.7\application/contents/temp/temD9A9.tmp): Permission denied ~ MODPATH\tpl\classes\Kohana\Tpl.php [ 660 ] in :
2016-01-04 09:37:09 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unlink(C:\\wamp\\...', 'C:\\wamp\\www\\der...', 660, Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\modules\tpl\classes\Kohana\Tpl.php(660): unlink('C:\\wamp\\www\\der...')
#2 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Template.php(526): Kohana_Tpl->render()
#3 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Home.php(93): Controller_Backend_Template->after()
#4 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(87): Controller_Backend_Home->after()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Backend_Home))
#7 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#10 {main} in :
2016-01-04 09:42:09 --- CRITICAL: HTTP_Exception_404 [ 404 ]:  ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Imagefly.php:92
2016-01-04 09:42:09 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Imagefly.php(92): Kohana_HTTP_Exception::factory(404)
#1 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(84): Controller_Imagefly->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Imagefly))
#4 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#7 {main} in C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Imagefly.php:92
2016-01-04 09:42:09 --- CRITICAL: HTTP_Exception_404 [ 404 ]:  ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Imagefly.php:92
2016-01-04 09:42:09 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Imagefly.php(92): Kohana_HTTP_Exception::factory(404)
#1 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(84): Controller_Imagefly->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Imagefly))
#4 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#7 {main} in C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Imagefly.php:92
2016-01-04 09:42:17 --- CRITICAL: ErrorException [ 2 ]: unlink(C:\wamp\www\deraemon-cms_0.8.7\application/contents/temp/tem8C3D.tmp): Permission denied ~ MODPATH\tpl\classes\Kohana\Tpl.php [ 660 ] in :
2016-01-04 09:42:17 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unlink(C:\\wamp\\...', 'C:\\wamp\\www\\der...', 660, Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\modules\tpl\classes\Kohana\Tpl.php(660): unlink('C:\\wamp\\www\\der...')
#2 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Template.php(526): Kohana_Tpl->render()
#3 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Items.php(2017): Controller_Backend_Template->after()
#4 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(87): Controller_Backend_Items->after()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Backend_Items))
#7 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#10 {main} in :
2016-01-04 09:42:22 --- CRITICAL: HTTP_Exception_404 [ 404 ]:  ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Imagefly.php:92
2016-01-04 09:42:22 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Imagefly.php(92): Kohana_HTTP_Exception::factory(404)
#1 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(84): Controller_Imagefly->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Imagefly))
#4 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#7 {main} in C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Imagefly.php:92
2016-01-04 09:48:07 --- CRITICAL: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: admin/items/page/content/1admin/directuser ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 09:48:07 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(975): Kohana_HTTP_Exception::factory(404, 'Unable to find ...', Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#2 {main} in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php:975
2016-01-04 09:48:44 --- CRITICAL: ErrorException [ 2 ]: unlink(C:\wamp\www\deraemon-cms_0.8.7\application/contents/temp/tem710F.tmp): Permission denied ~ MODPATH\tpl\classes\Kohana\Tpl.php [ 666 ] in :
2016-01-04 09:48:44 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unlink(C:\\wamp\\...', 'C:\\wamp\\www\\der...', 666, Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\modules\tpl\classes\Kohana\Tpl.php(666): unlink('C:\\wamp\\www\\der...')
#2 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Template.php(526): Kohana_Tpl->render()
#3 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(87): Controller_Backend_Template->after()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Backend_Auth))
#6 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#8 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#9 {main} in :
2016-01-04 09:49:59 --- CRITICAL: ErrorException [ 2 ]: unlink(C:\wamp\www\deraemon-cms_0.8.7\application/contents/temp/tem99BB.tmp): Permission denied ~ MODPATH\tpl\classes\Kohana\Tpl.php [ 666 ] in :
2016-01-04 09:49:59 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unlink(C:\\wamp\\...', 'C:\\wamp\\www\\der...', 666, Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\modules\tpl\classes\Kohana\Tpl.php(666): unlink('C:\\wamp\\www\\der...')
#2 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Template.php(526): Kohana_Tpl->render()
#3 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(87): Controller_Backend_Template->after()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Backend_Auth))
#6 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#8 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#9 {main} in :
2016-01-04 09:50:16 --- CRITICAL: ErrorException [ 2 ]: unlink(C:\wamp\www\deraemon-cms_0.8.7\application/contents/temp/temD8A0.tmp): Permission denied ~ MODPATH\tpl\classes\Kohana\Tpl.php [ 666 ] in :
2016-01-04 09:50:16 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unlink(C:\\wamp\\...', 'C:\\wamp\\www\\der...', 666, Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\modules\tpl\classes\Kohana\Tpl.php(666): unlink('C:\\wamp\\www\\der...')
#2 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Template.php(526): Kohana_Tpl->render()
#3 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Users.php(950): Controller_Backend_Template->after()
#4 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(87): Controller_Backend_Users->after()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Backend_Users))
#7 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#10 {main} in :
2016-01-04 09:50:36 --- CRITICAL: HTTP_Exception_404 [ 404 ]:  ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Imagefly.php:75
2016-01-04 09:50:36 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Imagefly.php(75): Kohana_HTTP_Exception::factory(404)
#1 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(84): Controller_Imagefly->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Imagefly))
#4 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#7 {main} in C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Imagefly.php:75
2016-01-04 09:50:41 --- CRITICAL: ErrorException [ 2 ]: unlink(C:\wamp\www\deraemon-cms_0.8.7\application/contents/temp/tem3B5B.tmp): Permission denied ~ MODPATH\tpl\classes\Kohana\Tpl.php [ 666 ] in :
2016-01-04 09:50:41 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unlink(C:\\wamp\\...', 'C:\\wamp\\www\\der...', 666, Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\modules\tpl\classes\Kohana\Tpl.php(666): unlink('C:\\wamp\\www\\der...')
#2 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Template.php(526): Kohana_Tpl->render()
#3 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Users.php(950): Controller_Backend_Template->after()
#4 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(87): Controller_Backend_Users->after()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Backend_Users))
#7 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#10 {main} in :
2016-01-04 09:50:50 --- CRITICAL: ErrorException [ 2 ]: unlink(C:\wamp\www\deraemon-cms_0.8.7\application/contents/temp/tem5F18.tmp): Permission denied ~ MODPATH\tpl\classes\Kohana\Tpl.php [ 666 ] in :
2016-01-04 09:50:50 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unlink(C:\\wamp\\...', 'C:\\wamp\\www\\der...', 666, Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\modules\tpl\classes\Kohana\Tpl.php(666): unlink('C:\\wamp\\www\\der...')
#2 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Template.php(526): Kohana_Tpl->render()
#3 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Users.php(950): Controller_Backend_Template->after()
#4 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(87): Controller_Backend_Users->after()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Backend_Users))
#7 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#10 {main} in :
2016-01-04 09:51:06 --- CRITICAL: ErrorException [ 2 ]: unlink(C:\wamp\www\deraemon-cms_0.8.7\application/contents/temp/tem9D82.tmp): No such file or directory ~ MODPATH\tpl\classes\Kohana\Tpl.php [ 666 ] in :
2016-01-04 09:51:06 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unlink(C:\\wamp\\...', 'C:\\wamp\\www\\der...', 666, Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\modules\tpl\classes\Kohana\Tpl.php(666): unlink('C:\\wamp\\www\\der...')
#2 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Template.php(526): Kohana_Tpl->render()
#3 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Settings.php(233): Controller_Backend_Template->after()
#4 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(87): Controller_Backend_Settings->after()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Backend_Settings))
#7 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#10 {main} in :
2016-01-04 10:05:45 --- CRITICAL: ErrorException [ 2 ]: unlink(C:\wamp\www\deraemon-cms_0.8.7\application/contents/temp/tem6F4.tmp): Permission denied ~ MODPATH\tpl\classes\Kohana\Tpl.php [ 666 ] in :
2016-01-04 10:05:45 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unlink(C:\\wamp\\...', 'C:\\wamp\\www\\der...', 666, Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\modules\tpl\classes\Kohana\Tpl.php(666): unlink('C:\\wamp\\www\\der...')
#2 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Template.php(526): Kohana_Tpl->render()
#3 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(87): Controller_Backend_Template->after()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Backend_Auth))
#6 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#8 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#9 {main} in :
2016-01-04 10:07:21 --- CRITICAL: ErrorException [ 2 ]: unlink(C:\wamp\www\deraemon-cms_0.8.7\application/contents/temp/tem7E45.tmp): No such file or directory ~ MODPATH\tpl\classes\Kohana\Tpl.php [ 666 ] in :
2016-01-04 10:07:21 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unlink(C:\\wamp\\...', 'C:\\wamp\\www\\der...', 666, Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\modules\tpl\classes\Kohana\Tpl.php(666): unlink('C:\\wamp\\www\\der...')
#2 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Template.php(526): Kohana_Tpl->render()
#3 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Users.php(950): Controller_Backend_Template->after()
#4 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(87): Controller_Backend_Users->after()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Backend_Users))
#7 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#10 {main} in :
2016-01-04 10:07:29 --- CRITICAL: ErrorException [ 2 ]: unlink(C:\wamp\www\deraemon-cms_0.8.7\application/contents/temp/tem9E44.tmp): Permission denied ~ MODPATH\tpl\classes\Kohana\Tpl.php [ 666 ] in :
2016-01-04 10:07:29 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unlink(C:\\wamp\\...', 'C:\\wamp\\www\\der...', 666, Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\modules\tpl\classes\Kohana\Tpl.php(666): unlink('C:\\wamp\\www\\der...')
#2 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Template.php(526): Kohana_Tpl->render()
#3 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Users.php(950): Controller_Backend_Template->after()
#4 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(87): Controller_Backend_Users->after()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Backend_Users))
#7 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#10 {main} in :
2016-01-04 10:07:41 --- CRITICAL: HTTP_Exception_404 [ 404 ]:  ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Imagefly.php:75
2016-01-04 10:07:41 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Imagefly.php(75): Kohana_HTTP_Exception::factory(404)
#1 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(84): Controller_Imagefly->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Imagefly))
#4 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#7 {main} in C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Imagefly.php:75
2016-01-04 10:08:01 --- CRITICAL: ErrorException [ 2 ]: unlink(C:\wamp\www\deraemon-cms_0.8.7\application/contents/temp/tem1C47.tmp): Permission denied ~ MODPATH\tpl\classes\Kohana\Tpl.php [ 666 ] in :
2016-01-04 10:08:01 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unlink(C:\\wamp\\...', 'C:\\wamp\\www\\der...', 666, Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\modules\tpl\classes\Kohana\Tpl.php(666): unlink('C:\\wamp\\www\\der...')
#2 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Template.php(526): Kohana_Tpl->render()
#3 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Wrappers.php(291): Controller_Backend_Template->after()
#4 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(87): Controller_Backend_Wrappers->after()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Backend_Wrappers))
#7 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#10 {main} in :
2016-01-04 10:13:05 --- CRITICAL: HTTP_Exception_404 [ 404 ]:  ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Media.php:86
2016-01-04 10:13:05 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Media.php(86): Kohana_HTTP_Exception::factory(404)
#1 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(84): Controller_Media->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Media))
#4 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#7 {main} in C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Media.php:86
2016-01-04 10:15:38 --- CRITICAL: HTTP_Exception_404 [ 404 ]:  ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Media.php:86
2016-01-04 10:15:38 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Media.php(86): Kohana_HTTP_Exception::factory(404)
#1 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(84): Controller_Media->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Media))
#4 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#7 {main} in C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Media.php:86
2016-01-04 10:16:03 --- CRITICAL: HTTP_Exception_404 [ 404 ]:  ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Media.php:86
2016-01-04 10:16:03 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Media.php(86): Kohana_HTTP_Exception::factory(404)
#1 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(84): Controller_Media->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Media))
#4 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#7 {main} in C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Media.php:86
2016-01-04 14:01:24 --- CRITICAL: HTTP_Exception_404 [ 404 ]: The requested URL product1admin/directuser was not found on this server. ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php:79
2016-01-04 14:01:24 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(79): Kohana_HTTP_Exception::factory(404, 'The requested U...', Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#2 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#3 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#4 {main} in C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php:79
2016-01-04 14:04:20 --- CRITICAL: ErrorException [ 2 ]: unlink(C:\wamp\www\deraemon-cms_0.8.7\application/contents/temp/tem7649.tmp): Permission denied ~ MODPATH\tpl\classes\Kohana\Tpl.php [ 666 ] in :
2016-01-04 14:04:20 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unlink(C:\\wamp\\...', 'C:\\wamp\\www\\der...', 666, Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\modules\tpl\classes\Kohana\Tpl.php(666): unlink('C:\\wamp\\www\\der...')
#2 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Template.php(526): Kohana_Tpl->render()
#3 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(87): Controller_Backend_Template->after()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Backend_Auth))
#6 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#8 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#9 {main} in :
2016-01-04 14:05:19 --- CRITICAL: ErrorException [ 2 ]: unlink(C:\wamp\www\deraemon-cms_0.8.7\application/contents/temp/tem5B92.tmp): Permission denied ~ MODPATH\tpl\classes\Kohana\Tpl.php [ 666 ] in :
2016-01-04 14:05:19 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unlink(C:\\wamp\\...', 'C:\\wamp\\www\\der...', 666, Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\modules\tpl\classes\Kohana\Tpl.php(666): unlink('C:\\wamp\\www\\der...')
#2 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Template.php(526): Kohana_Tpl->render()
#3 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Home.php(93): Controller_Backend_Template->after()
#4 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(87): Controller_Backend_Home->after()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Backend_Home))
#7 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#10 {main} in :