<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2015-12-28 02:12:47 --- CRITICAL: ErrorException [ 2 ]: unlink(C:\wamp\www\deraemon-cms_0.8.5\application/contents/temp/temBCD6.tmp): Permission denied ~ MODPATH\tpl\classes\Kohana\Tpl.php [ 666 ] in :
2015-12-28 02:12:47 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unlink(C:\\wamp\\...', 'C:\\wamp\\www\\der...', 666, Array)
#1 C:\wamp\www\deraemon-cms_0.8.5\modules\tpl\classes\Kohana\Tpl.php(666): unlink('C:\\wamp\\www\\der...')
#2 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Backend\Template.php(526): Kohana_Tpl->render()
#3 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Backend\Emails.php(867): Controller_Backend_Template->after()
#4 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Controller.php(87): Controller_Backend_Emails->after()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Backend_Emails))
#7 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 C:\wamp\www\deraemon-cms_0.8.5\index.php(130): Kohana_Request->execute()
#10 {main} in :
2015-12-28 02:19:01 --- CRITICAL: ErrorException [ 2 ]: unlink(C:\wamp\www\deraemon-cms_0.8.5\application/contents/temp/tem7046.tmp): Permission denied ~ MODPATH\tpl\classes\Kohana\Tpl.php [ 666 ] in :
2015-12-28 02:19:01 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unlink(C:\\wamp\\...', 'C:\\wamp\\www\\der...', 666, Array)
#1 C:\wamp\www\deraemon-cms_0.8.5\modules\tpl\classes\Kohana\Tpl.php(666): unlink('C:\\wamp\\www\\der...')
#2 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Backend\Template.php(526): Kohana_Tpl->render()
#3 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Backend\Items.php(2017): Controller_Backend_Template->after()
#4 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Controller.php(87): Controller_Backend_Items->after()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Backend_Items))
#7 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 C:\wamp\www\deraemon-cms_0.8.5\index.php(130): Kohana_Request->execute()
#10 {main} in :
2015-12-28 03:27:08 --- CRITICAL: HTTP_Exception_404 [ 404 ]:  ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:27:08 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php(86): Kohana_HTTP_Exception::factory(404)
#1 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Controller.php(84): Controller_Media->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Media))
#4 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 C:\wamp\www\deraemon-cms_0.8.5\index.php(130): Kohana_Request->execute()
#7 {main} in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:28:00 --- CRITICAL: HTTP_Exception_404 [ 404 ]:  ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:28:00 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php(86): Kohana_HTTP_Exception::factory(404)
#1 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Controller.php(84): Controller_Media->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Media))
#4 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 C:\wamp\www\deraemon-cms_0.8.5\index.php(130): Kohana_Request->execute()
#7 {main} in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:29:58 --- CRITICAL: HTTP_Exception_404 [ 404 ]:  ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:29:58 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php(86): Kohana_HTTP_Exception::factory(404)
#1 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Controller.php(84): Controller_Media->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Media))
#4 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 C:\wamp\www\deraemon-cms_0.8.5\index.php(130): Kohana_Request->execute()
#7 {main} in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:30:09 --- CRITICAL: HTTP_Exception_404 [ 404 ]:  ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:30:09 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php(86): Kohana_HTTP_Exception::factory(404)
#1 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Controller.php(84): Controller_Media->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Media))
#4 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 C:\wamp\www\deraemon-cms_0.8.5\index.php(130): Kohana_Request->execute()
#7 {main} in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:31:21 --- CRITICAL: ErrorException [ 2 ]: unlink(C:\wamp\www\deraemon-cms_0.8.5\application/contents/temp/temAACC.tmp): Permission denied ~ MODPATH\tpl\classes\Kohana\Tpl.php [ 666 ] in :
2015-12-28 03:31:21 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unlink(C:\\wamp\\...', 'C:\\wamp\\www\\der...', 666, Array)
#1 C:\wamp\www\deraemon-cms_0.8.5\modules\tpl\classes\Kohana\Tpl.php(666): unlink('C:\\wamp\\www\\der...')
#2 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Backend\Template.php(526): Kohana_Tpl->render()
#3 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Backend\Settings.php(233): Controller_Backend_Template->after()
#4 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Controller.php(87): Controller_Backend_Settings->after()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Backend_Settings))
#7 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 C:\wamp\www\deraemon-cms_0.8.5\index.php(130): Kohana_Request->execute()
#10 {main} in :
2015-12-28 03:31:24 --- CRITICAL: ErrorException [ 2 ]: unlink(C:\wamp\www\deraemon-cms_0.8.5\application/contents/temp/temB648.tmp): Permission denied ~ MODPATH\tpl\classes\Kohana\Tpl.php [ 666 ] in :
2015-12-28 03:31:24 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unlink(C:\\wamp\\...', 'C:\\wamp\\www\\der...', 666, Array)
#1 C:\wamp\www\deraemon-cms_0.8.5\modules\tpl\classes\Kohana\Tpl.php(666): unlink('C:\\wamp\\www\\der...')
#2 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Backend\Template.php(526): Kohana_Tpl->render()
#3 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Backend\Settings.php(233): Controller_Backend_Template->after()
#4 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Controller.php(87): Controller_Backend_Settings->after()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Backend_Settings))
#7 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 C:\wamp\www\deraemon-cms_0.8.5\index.php(130): Kohana_Request->execute()
#10 {main} in :
2015-12-28 03:32:04 --- CRITICAL: HTTP_Exception_404 [ 404 ]:  ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:32:04 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php(86): Kohana_HTTP_Exception::factory(404)
#1 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Controller.php(84): Controller_Media->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Media))
#4 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 C:\wamp\www\deraemon-cms_0.8.5\index.php(130): Kohana_Request->execute()
#7 {main} in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:32:43 --- CRITICAL: HTTP_Exception_404 [ 404 ]:  ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:32:43 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php(86): Kohana_HTTP_Exception::factory(404)
#1 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Controller.php(84): Controller_Media->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Media))
#4 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 C:\wamp\www\deraemon-cms_0.8.5\index.php(130): Kohana_Request->execute()
#7 {main} in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:32:46 --- CRITICAL: HTTP_Exception_404 [ 404 ]:  ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:32:46 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php(86): Kohana_HTTP_Exception::factory(404)
#1 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Controller.php(84): Controller_Media->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Media))
#4 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 C:\wamp\www\deraemon-cms_0.8.5\index.php(130): Kohana_Request->execute()
#7 {main} in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:34:06 --- CRITICAL: HTTP_Exception_404 [ 404 ]:  ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:34:06 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php(86): Kohana_HTTP_Exception::factory(404)
#1 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Controller.php(84): Controller_Media->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Media))
#4 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 C:\wamp\www\deraemon-cms_0.8.5\index.php(130): Kohana_Request->execute()
#7 {main} in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:34:21 --- CRITICAL: HTTP_Exception_404 [ 404 ]:  ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:34:21 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php(86): Kohana_HTTP_Exception::factory(404)
#1 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Controller.php(84): Controller_Media->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Media))
#4 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 C:\wamp\www\deraemon-cms_0.8.5\index.php(130): Kohana_Request->execute()
#7 {main} in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:43:15 --- CRITICAL: HTTP_Exception_404 [ 404 ]:  ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:43:15 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php(86): Kohana_HTTP_Exception::factory(404)
#1 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Controller.php(84): Controller_Media->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Media))
#4 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 C:\wamp\www\deraemon-cms_0.8.5\index.php(130): Kohana_Request->execute()
#7 {main} in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:43:26 --- CRITICAL: HTTP_Exception_404 [ 404 ]:  ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:43:26 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php(86): Kohana_HTTP_Exception::factory(404)
#1 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Controller.php(84): Controller_Media->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Media))
#4 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 C:\wamp\www\deraemon-cms_0.8.5\index.php(130): Kohana_Request->execute()
#7 {main} in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:43:52 --- CRITICAL: HTTP_Exception_404 [ 404 ]:  ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:43:52 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php(86): Kohana_HTTP_Exception::factory(404)
#1 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Controller.php(84): Controller_Media->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Media))
#4 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 C:\wamp\www\deraemon-cms_0.8.5\index.php(130): Kohana_Request->execute()
#7 {main} in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:44:12 --- CRITICAL: HTTP_Exception_404 [ 404 ]:  ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:44:12 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php(86): Kohana_HTTP_Exception::factory(404)
#1 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Controller.php(84): Controller_Media->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Media))
#4 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 C:\wamp\www\deraemon-cms_0.8.5\index.php(130): Kohana_Request->execute()
#7 {main} in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:44:52 --- CRITICAL: HTTP_Exception_404 [ 404 ]:  ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:44:52 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php(86): Kohana_HTTP_Exception::factory(404)
#1 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Controller.php(84): Controller_Media->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Media))
#4 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 C:\wamp\www\deraemon-cms_0.8.5\index.php(130): Kohana_Request->execute()
#7 {main} in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:45:06 --- CRITICAL: HTTP_Exception_404 [ 404 ]:  ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:45:06 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php(86): Kohana_HTTP_Exception::factory(404)
#1 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Controller.php(84): Controller_Media->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Media))
#4 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 C:\wamp\www\deraemon-cms_0.8.5\index.php(130): Kohana_Request->execute()
#7 {main} in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:45:55 --- CRITICAL: HTTP_Exception_404 [ 404 ]:  ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:45:55 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php(86): Kohana_HTTP_Exception::factory(404)
#1 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Controller.php(84): Controller_Media->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Media))
#4 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 C:\wamp\www\deraemon-cms_0.8.5\index.php(130): Kohana_Request->execute()
#7 {main} in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:47:58 --- CRITICAL: HTTP_Exception_404 [ 404 ]:  ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:47:58 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php(86): Kohana_HTTP_Exception::factory(404)
#1 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Controller.php(84): Controller_Media->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Media))
#4 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 C:\wamp\www\deraemon-cms_0.8.5\index.php(130): Kohana_Request->execute()
#7 {main} in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:48:31 --- CRITICAL: HTTP_Exception_404 [ 404 ]:  ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:48:31 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php(86): Kohana_HTTP_Exception::factory(404)
#1 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Controller.php(84): Controller_Media->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Media))
#4 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 C:\wamp\www\deraemon-cms_0.8.5\index.php(130): Kohana_Request->execute()
#7 {main} in C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Media.php:86
2015-12-28 03:51:27 --- CRITICAL: ErrorException [ 2 ]: unlink(C:\wamp\www\deraemon-cms_0.8.5\application/contents/temp/temFC6.tmp): Permission denied ~ MODPATH\tpl\classes\Kohana\Tpl.php [ 666 ] in :
2015-12-28 03:51:27 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unlink(C:\\wamp\\...', 'C:\\wamp\\www\\der...', 666, Array)
#1 C:\wamp\www\deraemon-cms_0.8.5\modules\tpl\classes\Kohana\Tpl.php(666): unlink('C:\\wamp\\www\\der...')
#2 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Backend\Template.php(526): Kohana_Tpl->render()
#3 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Backend\Emails.php(867): Controller_Backend_Template->after()
#4 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Controller.php(87): Controller_Backend_Emails->after()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Backend_Emails))
#7 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 C:\wamp\www\deraemon-cms_0.8.5\index.php(130): Kohana_Request->execute()
#10 {main} in :
2015-12-28 03:53:23 --- CRITICAL: ErrorException [ 2 ]: unlink(C:\wamp\www\deraemon-cms_0.8.5\application/contents/temp/temD517.tmp): Permission denied ~ MODPATH\tpl\classes\Kohana\Tpl.php [ 666 ] in :
2015-12-28 03:53:23 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unlink(C:\\wamp\\...', 'C:\\wamp\\www\\der...', 666, Array)
#1 C:\wamp\www\deraemon-cms_0.8.5\modules\tpl\classes\Kohana\Tpl.php(666): unlink('C:\\wamp\\www\\der...')
#2 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Backend\Template.php(526): Kohana_Tpl->render()
#3 C:\wamp\www\deraemon-cms_0.8.5\modules\cms\classes\Controller\Backend\Author.php(899): Controller_Backend_Template->after()
#4 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Controller.php(87): Controller_Backend_Author->after()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Backend_Author))
#7 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 C:\wamp\www\deraemon-cms_0.8.5\index.php(130): Kohana_Request->execute()
#10 {main} in :
2015-12-28 04:01:39 --- CRITICAL: HTTP_Exception_404 [ 404 ]: The requested URL author/forgot was not found on this server. ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client\Internal.php:79
2015-12-28 04:01:39 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client\Internal.php(79): Kohana_HTTP_Exception::factory(404, 'The requested U...', Array)
#1 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#2 C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#3 C:\wamp\www\deraemon-cms_0.8.5\index.php(130): Kohana_Request->execute()
#4 {main} in C:\wamp\www\deraemon-cms_0.8.5\system\classes\Kohana\Request\Client\Internal.php:79