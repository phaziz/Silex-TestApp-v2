<?php

	use Silex\Application;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;

	/*
	 * ROUTING ./
	 * */
	$app -> get('/', function () use ($app)
		{
			return $app['twig'] -> render('index.html',
				array
				(
			    	'HTML_TITLE' => 'Hello Christian, SILEX is working...'
				)
			);
			
		}
	) -> bind('homepage');

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
			    	'MONOLOG_TESTING_RESULT' => $MONOLOG_TESTING_RESULT,
			    	'HTML_TITLE' => 'Monolog is working...'
				)
			);
		}
	) -> bind('monolog-test');

	$app -> get('/test/httpcache', function () use ($app, $response)
		{
			$body = $app['twig'] -> render('httpcache.html',array('HTML_TITLE' => 'HTTP CACHE is working...'));
 			return new Response($body, 200, array('Cache-Control' => 's-maxage=86400, public'));
		}
	) -> bind('httpcache-test');
	

