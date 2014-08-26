<?php

	/*
	 * NICLUDING COMPOSER AUTOLOAD
	 * */
	require_once __DIR__.'/../vendor/autoload.php';

	/*
	 * CREATING NEW SILEX INSTANCE
	 * */
	$app = new Silex\Application();
	$app['debug'] = true;

	/*
	 * REGISTERING ADDITIONAL MODULES START
	 * */
	$app -> register(new Silex\Provider\UrlGeneratorServiceProvider());
	$app -> register(new Silex\Provider\DoctrineServiceProvider(),
        array
            (
                'db.options' => array
                (
                    'driver'    => 'pdo_mysql',
                    'host'      => 'localhost',
                    'dbname'    => 'doctrine-test',
                    'user'      => 'root',
                    'password'  => 'root',
                    'charset'   => 'utf8'
                )
            )
	    );
	$app -> register(new Silex\Provider\MonologServiceProvider(),
		array
		(
	    	'monolog.logfile' => __DIR__ . '/../app/logfiles/' . date('Ymd') . '.txt'
		)
	);
	$app -> register(new Silex\Provider\HttpCacheServiceProvider(),
		array
		(
		    'http_cache.cache_dir' => __DIR__.'/../app/cache/',
		    'http_cache.esi' => null,
		)
	);
	$app -> register(new Silex\Provider\TwigServiceProvider(),
		array
		(
	    	'twig.path' => __DIR__ . '/../app/views'
		)
	);
	/*
	 * REGISTERING ADDITIONAL MODULES END
	 * */

	/*
	 * INCLUDING APPLICATION ROUTES
	 * */
	require_once __DIR__ . '/../app/routes/routes.php';

	/*
	 * ERROR HANDLING START
	 * */
	$app -> error(function (\Exception $e, $code) use ($app)
		{
			if ($app['debug'])
			{
		    	return;
			}

			switch ($code)
			{
		    	case 404:
		        	return '404 not found';
		    		break;
				default:
		    		return 'ERROR: ' . $code;
		    }
		}
	);
	/*
	 * ERROR HANDLING END
	 * */

	/*
	 * RUNNING THE APP
	 * */
	if ($app['debug'] == true)
	{
		$app -> run();
	}
	else
	{
		$app['http_cache'] -> run();
	}