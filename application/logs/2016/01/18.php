<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2016-01-18 02:39:51 --- CRITICAL: ErrorException [ 4 ]: syntax error, unexpected '"item"' (T_CONSTANT_ENCAPSED_STRING), expecting variable (T_VARIABLE) or '$' ~ APPPATH\contents\temp\tem93DF.tmp [ 108 ] in :
2016-01-18 02:39:51 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main} in :
2016-01-18 02:40:02 --- CRITICAL: ErrorException [ 1 ]: Call to undefined method Cms_Functions::get_images() ~ APPPATH\contents\temp\temBFC2.tmp [ 367 ] in :
2016-01-18 02:40:02 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()
#1 {main} in :
2016-01-18 02:57:56 --- CRITICAL: HTTP_Exception_404 [ 404 ]:  ~ SYSPATH\classes\Kohana\HTTP\Exception.php [ 17 ] in C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Media.php:86
2016-01-18 02:57:56 --- DEBUG: #0 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Media.php(86): Kohana_HTTP_Exception::factory(404)
#1 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(84): Controller_Media->action_index()
#2 [internal function]: Kohana_Controller->execute()
#3 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Media))
#4 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#5 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#6 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#7 {main} in C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Media.php:86
2016-01-18 03:13:31 --- CRITICAL: ErrorException [ 2 ]: unlink(C:\wamp\www\deraemon-cms_0.8.7\application/contents/temp/tem6990.tmp): Permission denied ~ MODPATH\tpl\classes\Kohana\Tpl.php [ 666 ] in :
2016-01-18 03:13:31 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unlink(C:\\wamp\\...', 'C:\\wamp\\www\\der...', 666, Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\modules\tpl\classes\Kohana\Tpl.php(666): unlink('C:\\wamp\\www\\der...')
#2 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Template.php(526): Kohana_Tpl->render()
#3 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Items.php(2017): Controller_Backend_Template->after()
#4 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(87): Controller_Backend_Items->after()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Backend_Items))
#7 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#10 {main} in :
2016-01-18 03:14:22 --- CRITICAL: ErrorException [ 2 ]: unlink(C:\wamp\www\deraemon-cms_0.8.7\application/contents/temp/tem2DA2.tmp): Permission denied ~ MODPATH\tpl\classes\Kohana\Tpl.php [ 666 ] in :
2016-01-18 03:14:22 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unlink(C:\\wamp\\...', 'C:\\wamp\\www\\der...', 666, Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\modules\tpl\classes\Kohana\Tpl.php(666): unlink('C:\\wamp\\www\\der...')
#2 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Template.php(526): Kohana_Tpl->render()
#3 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Items.php(2017): Controller_Backend_Template->after()
#4 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(87): Controller_Backend_Items->after()
#5 [internal function]: Kohana_Controller->execute()
#6 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Backend_Items))
#7 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#10 {main} in :
2016-01-18 21:19:34 --- CRITICAL: ErrorException [ 2 ]: unlink(C:\wamp\www\deraemon-cms_0.8.7\application/contents/temp/temB91D.tmp): Permission denied ~ MODPATH\tpl\classes\Kohana\Tpl.php [ 666 ] in :
2016-01-18 21:19:34 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unlink(C:\\wamp\\...', 'C:\\wamp\\www\\der...', 666, Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\modules\tpl\classes\Kohana\Tpl.php(666): unlink('C:\\wamp\\www\\der...')
#2 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Template.php(526): Kohana_Tpl->render()
#3 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(87): Controller_Backend_Template->after()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Backend_Auth))
#6 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#8 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#9 {main} in :
2016-01-18 22:02:31 --- CRITICAL: ErrorException [ 2 ]: unlink(C:\wamp\www\deraemon-cms_0.8.7\application/contents/temp/temBBA.tmp): Permission denied ~ MODPATH\tpl\classes\Kohana\Tpl.php [ 666 ] in :
2016-01-18 22:02:31 --- DEBUG: #0 [internal function]: Kohana_Core::error_handler(2, 'unlink(C:\\wamp\\...', 'C:\\wamp\\www\\der...', 666, Array)
#1 C:\wamp\www\deraemon-cms_0.8.7\modules\tpl\classes\Kohana\Tpl.php(666): unlink('C:\\wamp\\www\\der...')
#2 C:\wamp\www\deraemon-cms_0.8.7\modules\cms\classes\Controller\Backend\Template.php(526): Kohana_Tpl->render()
#3 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Controller.php(87): Controller_Backend_Template->after()
#4 [internal function]: Kohana_Controller->execute()
#5 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client\Internal.php(97): ReflectionMethod->invoke(Object(Controller_Backend_Auth))
#6 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request\Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 C:\wamp\www\deraemon-cms_0.8.7\system\classes\Kohana\Request.php(986): Kohana_Request_Client->execute(Object(Request))
#8 C:\wamp\www\deraemon-cms_0.8.7\index.php(130): Kohana_Request->execute()
#9 {main} in :