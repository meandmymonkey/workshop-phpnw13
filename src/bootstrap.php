<?php

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Router;
use Symfony\Component\Templating\Helper\SlotsHelper;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Yaml\Yaml;
use Workshop\Controller\TaskController;
use Workshop\Model\TaskRepository;
use Workshop\Templating\RouterHelper;
use Workshop\Util\SingleControllerResolver;

/*
 * Bootstrap the application. In a real world project, you'd want to use a DIC for this
 * instead of putting everything in a bootstrap file...
 */

$setup = function()
{
    // Load and parse configuration

    $cachePath = __DIR__ . '/../cache/config.php';
    $configPath = __DIR__ . '/../config/config.yml';
    $configCache = new ConfigCache($cachePath, true);

    if (!$configCache->isFresh()) {
        $resource = new FileResource($configPath);

        $code = '<?php return ' . var_export(Yaml::parse($configPath), true) . ';';

        $configCache->write($code, array($resource));
    }

    $config = require $cachePath;


    // Set up database access

    $taskRepository = new TaskRepository(
        new \PDO(
            $config['db']['dsn'],
            $config['db']['username'],
            $config['db']['password'],
            array(
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
            )
        )
    );


    // Router

    $router = new Router(
        new YamlFileLoader(new FileLocator(__DIR__ . '/../config')),
        'routing.yml',
        array()
    );


    // Templating

    $templating = new PhpEngine(
        new TemplateNameParser(),
        new FilesystemLoader(
            array(__DIR__ . '/../views/%name%')
        )
    );
    $templating->addHelpers(
        array(
            new SlotsHelper(),
            new RouterHelper($router)
        )
    );


    // Event dispatcher setup

    $dispatcher = new EventDispatcher();
    $dispatcher->addSubscriber(
        new RouterListener($router->getMatcher())
    );


    // Controller and resolver

    $controller = new TaskController($taskRepository, $router->getGenerator(), $templating);
    $resolver = new SingleControllerResolver($controller);


    // Create the kernel

    return new HttpKernel($dispatcher, $resolver);
};

return $setup();
