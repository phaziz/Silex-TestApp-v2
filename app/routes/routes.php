<?php

	/*
	 * NAMESPACING
	 * */
	use Silex\Application;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpFoundation\RedirectResponse;
	use Symfony\Component\HttpKernel\HttpKernelInterface;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;



	/*
	 * ERROR HANDLING START
	 * */
    $app -> error(function (\Exception $e, $code) use ($app)
        {
            $templates = array
            (
                'errors/'.$code.'.html',
                'errors/'.substr($code, 0, 2).'x.html',
                'errors/'.substr($code, 0, 1).'xx.html',
                'errors/default.html',
            );

            return new Response($app['twig'] -> resolveTemplate($templates) -> render(array('_BASE_URL' => $app['base_url'],'CODE' => $code)), $code);
        }
    );



	/*
	 * ROUTING
	 * */
	$app -> get('/', function () use ($app)
		{
			return $app['twig'] -> render('index.html',
				array
				(
					'_BASE_URL' => $app['base_url'],
			    	'HTML_TITLE' => 'Hello Christian, SILEX is working...'
				)
			);
			
		}
	) -> bind('homepage');



	$app -> get('/test/forms', function () use ($app)
		{
			$FORM_ACTION = $app['base_url'] . 'test/forms';
			$FORM_METHOD = 'post';
			$FORM_ENCTYPE = 'application/x-www-form-urlencoded';
			$FORM_CSRF_TOKEN = crypt($app['token_salt']);
			$FORM_CSRF_TOKEN = str_replace('/','',$FORM_CSRF_TOKEN);

			return $app['twig'] -> render('forms_test.html',
				array
				(
					'_BASE_URL' => $app['base_url'],
			    	'HTML_TITLE' => 'Forms working...',
			    	'FORM_ACTION' => $FORM_ACTION . '/' . $FORM_CSRF_TOKEN,
			    	'FORM_METHOD' => $FORM_METHOD,
			    	'FORM_ENCTYPE' => $FORM_ENCTYPE,
			    	'FORM_CSRF_TOKEN' => $FORM_CSRF_TOKEN
				)
			);
		}
	) -> bind('forms-test');



	$app -> post('/test/forms/{csrf_token}', function ($csrf_token,Request $request) use ($app)
		{
			if($csrf_token == $app -> escape($request -> get('csrf_token')))
			{
				$_FORM_NAME = $app -> escape($request -> get('name'));
				$_FORM_VAL = $app -> escape($request -> get('val'));

				$app['db'] -> insert(
					'doctrinetest',
					array
					(
						'id' => NULL,
						'name' => $_FORM_NAME,
						'val' => $_FORM_VAL
					)
				);

			    $subRequest = Request::create('/test/doctrine', 'GET');
			    return $app -> handle($subRequest, HttpKernelInterface::SUB_REQUEST);
			}
			else
			{
				return 'FORM ERROR!';
			}
		}
	);



	$app -> get('/test/swiftmailer', function () use ($app)
		{
            $message = \Swift_Message::newInstance()
                     -> setSubject('SwiftMailer Test')
                     -> setFrom(array('XXXXX@gmail.com'))
                     -> setTo(array('XXXXX@gmail.com'))
                     -> setBody('SwiftMailer works...');

			if ($app['mailer'] ->send($message))
			{
			  $SWIFT_MAILER_RESULT = 'true';
			}
			else
			{
			  $SWIFT_MAILER_RESULT = 'true';
			}

			return $app['twig'] -> render('swiftmailer_test.html',
				array
				(
					'_BASE_URL' => $app['base_url'],
			    	'HTML_TITLE' => 'SwiftMailer is working...',
			    	'SWIFT_MAILER_RESULT' => $SWIFT_MAILER_RESULT
				)
			);
		}
	) -> bind('swiftmailer-test');



	$app -> get('/test/doctrine', function () use ($app)
		{
			$SQL  = "SELECT * FROM doctrinetest;";
			$TEST_DATA = $app['db'] -> prepare($SQL);
			$TEST_DATA -> execute();

			return $app['twig'] -> render('doctrine_test.html',
				array
				(
					'_BASE_URL' => $app['base_url'],
			    	'TEST_DATA' => $TEST_DATA,
			    	'HTML_TITLE' => 'Doctrine is working...'
				)
			);
		}
	) -> bind('doctrine-test');



	$app -> get('/test/monolog', function () use ($app)
		{
			$app['monolog'] -> addDebug('Testing the Monolog logging.');

			$LOGFILE = __DIR__ . '/../../app/logfiles/' . date(	'Ymd') . '.txt';

			if(file_exists($LOGFILE) && is_writeable($LOGFILE)  && is_readable($LOGFILE))
			{
				$MONOLOG_TESTING_RESULT = 'file_exists => true, is_writeable => true, is_readable => true';
			}
			else
			{
				$MONOLOG_TESTING_RESULT = 'file_exists => false, is_writeable => false, is_readable => false';
			}

			return $app['twig'] -> render('monolog_test.html',
				array
				(
					'_BASE_URL' => $app['base_url'],
			    	'MONOLOG_TESTING_RESULT' => $MONOLOG_TESTING_RESULT,
			    	'HTML_TITLE' => 'Monolog is working...'
				)
			);
		}
	) -> bind('monolog-test');



	$app -> get('/test/httpcache', function () use ($app, $response)
		{
			$body = $app['twig'] -> render('httpcache.html',array('_BASE_URL' => $app['base_url'], 'HTML_TITLE' => 'HTTP CACHE is working...'));
 			return new Response($body, 200, array('_BASE_URL' => $app['base_url'], 'Cache-Control' => 's-maxage=86400, public'));
		}
	) -> bind('httpcache-test');



	$app->get('/test/redirect-1', function () use ($app)
		{
		    return $app -> redirect('http://localhost:8888/localmamp/silex/web/test/redirect-2');
		}
	) -> bind('redirect-test');



	$app -> get('/test/forwarding', function () use ($app, $response, $request)
		{
		    $subRequest = Request::create('/test/redirect-2', 'GET');
		    return $app -> handle($subRequest, HttpKernelInterface::SUB_REQUEST);
		}
	) -> bind('forward-test');



	$app -> get('/test/redirect-2', function () use ($app, $response, $request)
		{
			return $app['twig'] -> render('redirect_test.html',
				array
				(
					'_BASE_URL' => $app['base_url'],
			    	'HTML_TITLE' => 'AppRedirecting is working...'
				)
			);
		}
	);