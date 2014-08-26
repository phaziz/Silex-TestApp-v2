<?php

	/*
	 * NICLUDING COMPOSER AUTOLOAD
	 * */
	require_once __DIR__.'/../vendor/autoload.php';

	/*
	 * CREATING NEW SILEX INSTANCE
	 * */
	$app = new Silex\Application();

	/*
	 * CONFIGURING THE APP
	 * */
	$app['debug'] = false;
	$app['base_url'] = 'http://localhost:8888/localmamp/silex/web/';
	$app['token_salt'] = ':)';

	/*
	 * REGISTERING ADDITIONAL MODULES START
	 * */
	$app -> register(new Silex\Provider\UrlGeneratorServiceProvider());

	$app -> register(new Silex\Provider\FormServiceProvider(),
		array
		(
	    	'form.registry' => $app -> share(function() use ($app)
	    		{
	        		$resolvedTypeFactory = new Symfony\Component\Form\ResolvedFormTypeFactory();
	        		return new Symfony\Component\Form\FormRegistry($app['form.extensions'], $resolvedTypeFactory);
	    		}
			),
	    	'form.factory' => $app->share(function() use ($app)
	    		{
	        		$resolvedTypeFactory = new Symfony\Component\Form\ResolvedFormTypeFactory();
	        		return new Symfony\Component\Form\FormFactory($app['form.registry'], $resolvedTypeFactory);
	    		}
			)
		)
	);

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

    $app -> register(new Silex\Provider\SwiftmailerServiceProvider());

    $app['swiftmailer.options'] = array(
        'host'       => 'smtp.gmail.com',
        'port'       => 465,
        'username'   => 'XXXXX@gmail.com',
        'password'   => 'XXXXX',
        'encryption' => 'ssl',
        'auth_mode'  => 'login'
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