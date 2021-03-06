<?php

declare(strict_types=1);

namespace Nymfonya\Component\Http\Reuse;

use Monolog\Logger;
use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use Nymfonya\Component\Pubsub\Dispatcher;
use Nymfonya\Component\Http\Kernel;
use Nymfonya\Component\Http\Middleware;
use Nymfonya\Component\Http\Request;
use Nymfonya\Component\Http\Response;
use Nymfonya\Component\Http\Router;
use ReflectionClass;

trait TKernel
{

    /**
     * app path
     *
     * @var string
     */
    protected $path;

    /**
     * app environment
     *
     * @var string
     */
    protected $env;

    /**
     * app config
     *
     * @var Config
     */
    protected $config;

    /**
     * service container
     *
     * @var Container
     */
    protected $container;

    /**
     * monolog logger
     *
     * @var Logger
     */
    protected $logger;

    /**
     * app request
     *
     * @var Request
     */
    protected $req;

    /**
     * app response
     *
     * @var Response
     */
    protected $res;

    /**
     * app router
     *
     * @var Router
     */
    protected $router;

    /**
     * app dispatcher
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * ctrl class name
     *
     * @var string
     */
    protected $className;

    /**
     * controller namespace
     *
     * @var string
     */
    protected $spacename;

    /**
     * controller instance
     *
     * @var mixed
     */
    protected $controller;

    /**
     * reflection class instance
     *
     * @var ReflectionClass
     */
    protected $reflector;

    /**
     * controller actions
     *
     * @var array
     */
    protected $actions;

    /**
     * controller action
     *
     * @var string
     */
    protected $action;

    /**
     * action annotations
     *
     * @var string
     */
    protected $actionAnnotations;

    /**
     * middlewares stack
     *
     * @var array
     */
    protected $middlewares;

    /**
     * run error
     *
     * @var Boolean
     */
    protected $error;

    /**
     * http status error code
     *
     * @var int
     */
    protected $errorCode;

    /**
     * error message
     *
     * @var string
     */
    protected $errorMsg;

    /**
     * return service container for service name
     *
     * @param string $serviceName
     * @throws Exception
     * @return object
     */
    public function getService(string $serviceName)
    {
        return $this->container->getService($serviceName);
    }

    /**
     * init kernel
     *
     * @param string $env
     * @param string $path
     * @return void
     */
    protected function init(string $env, string $path)
    {
        $this->env = $env;
        $this->path = $path;
        $this->error = true;
        $this->errorCode = Response::HTTP_NOT_FOUND;
        $this->errorMsg = 'Not Found';
        $this->setPath($path);
        $this->setConfig();
        $this->setContainer();
        $this->setDispatcher();
        $this->setRequest();
        $this->setResponse();
        $this->setLogger();
        $this->setRouter();
        $this->className = '';
        $this->actions = [];
        $this->action = '';
        $this->actionAnnotations = '';
    }

    /**
     * execute controller action in middleware core closure
     *
     * @return void
     */
    protected function execute(...$args)
    {
        if ($this->isValidAction() && is_object($this->controller)) {
            $slugs = (isset($args[0])) ? $args[0] : [];
            $resExec = $this->invokeAction($slugs);
            $this->error = ($resExec === false);
            $this->errorCode = ($this->error)
                ? Response::HTTP_INTERNAL_SERVER_ERROR
                : Response::HTTP_OK;
            $this->errorMsg = sprintf(
                'Execute %s',
                ($this->error) ? 'failed' : 'success'
            );
            $this->logger->debug($this->errorMsg);
        } else {
            $this->error = true;
            $this->errorMsg = 'Unknown endpoint';
            $this->errorCode = Response::HTTP_BAD_REQUEST;
        }
    }

    /**
     * invoke action from a controller an return exec code.
     * for testing purpose return retValue if false
     *
     * @param array $args
     * @return mixed
     */
    protected function invokeAction(...$args)
    {
        return call_user_func_array([$this->controller, $this->action], $args);
    }

    /**
     * return controller instance
     *
     * @return mixed
     */
    protected function getController()
    {
        return $this->controller;
    }

    /**
     * set middlewares from config then run before/after layers around core
     *
     */
    protected function setMiddleware()
    {
        $middlwaresConfig = $this->config->getSettings(
            Config::_MIDDLEWARES
        );
        $middlwaresClasses = array_keys($middlwaresConfig);
        foreach ($middlwaresClasses as $className) {
            $this->middlewares[$className] = new $className();
        }
        (new Middleware())->layer($this->middlewares)->peel(
            $this->container,
            function ($container) {
                $this->execute($this->router->getParams());
                return $container;
            }
        );
    }

    /**
     * set action annotations for runnig action
     *
     */
    protected function setActionAnnotations()
    {
        if ($this->isValidAction()) {
            $this->actionAnnotations = '';
            $refMethod = $this->reflector->getMethod($this->action);
            $docComment = $refMethod->getDocComment();
            $noComment = (false === $refMethod || false === $docComment);
            if (false === $noComment) {
                $this->actionAnnotations = (string) $docComment;
            }
            unset($refMethod, $docComment, $noComment);
        }
    }

    /**
     * return action docblock as string
     *
     * @return string
     */
    protected function getActionAnnotations(): string
    {
        return $this->actionAnnotations;
    }

    /**
     * return true if action is valid
     *
     * @return boolean
     */
    protected function isValidAction(): bool
    {
        return in_array($this->action, $this->actions);
    }

    /**
     * instanciate controller for a classname
     *
     * @return void
     */
    protected function setController()
    {
        $this->controller = new $this->className($this->container);
    }

    /**
     * set relflector on class name
     *
     */
    protected function setReflector()
    {
        $this->reflector = new \ReflectionClass($this->className);
    }

    /**
     * return reflexion class on current classname
     *
     * @return ReflectionClass
     */
    protected function getReflector(): ReflectionClass
    {
        return $this->reflector;
    }

    /**
     * return core controller action
     *
     * @return string
     */
    protected function getAction(): string
    {
        return $this->action;
    }

    /**
     * set public final actions from controller class name
     *
     */
    protected function setActions()
    {
        $methods = $this->getFinalMethods();
        $methodsName = array_map(function ($method) {
            return $method->name;
        }, $methods);
        $this->actions = array_merge($methodsName, ['preflight']);
    }

    /**
     * get final methods for the current classname
     *
     * @return array
     */
    protected function getFinalMethods(): array
    {
        return $this->getReflector()->getMethods(
            \ReflectionMethod::IS_FINAL
        );
    }

    /**
     * return public final actions from controller class name
     *
     */
    protected function getActions(): array
    {
        return $this->actions;
    }

    /**
     * set service container
     *
     */
    protected function setContainer()
    {
        $this->container = new Container(
            $this->config->getSettings(Config::_SERVICES)
        );
        $this->container->setService(
            \Nymfonya\Component\Http\Kernel::class,
            $this
        );
    }

    /**
     * get service container
     *
     */
    protected function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * set request
     *
     */
    protected function setRequest()
    {
        $this->req = $this->getService(Request::class);
    }

    /**
     * return request
     *
     */
    protected function getRequest(): Request
    {
        return $this->req;
    }

    /**
     * set response
     *
     */
    protected function setResponse()
    {
        $this->res = $this->getService(Response::class);
    }

    /**
     * return reponse
     *
     */
    protected function getResponse(): Response
    {
        return $this->res;
    }

    /**
     * set router
     *
     */
    protected function setRouter()
    {
        $this->router = $this->getService(Router::class);
    }

    /**
     * return router
     *
     */
    protected function getRouter(): Router
    {
        return $this->router;
    }

    /**
     * set controller class name
     *
     * @param array $routerGroups
     */
    protected function setClassname(array $routerGroups)
    {
        $this->className = $this->spacename
            . implode(
                '\\',
                array_map('ucfirst', explode('/', $routerGroups[0]))
            );
    }

    /**
     * set controller class name
     *
     */
    protected function getClassname(): string
    {
        return $this->className;
    }

    /**
     * set app config
     *
     */
    protected function setConfig()
    {
        $this->config = new Config(
            $this->env,
            $this->path . Kernel::PATH_CONFIG
        );
    }

    /**
     * returns config
     *
     * @return Config
     */
    protected function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * set app logger
     *
     */
    protected function setLogger()
    {
        $this->logger = $this->getService(\Monolog\Logger::class);
        $handlers = $this->logger->getHandlers();
        foreach ($handlers as $handlder) {
            if ($handlder instanceof \Monolog\Handler\RotatingFileHandler) {
                $patchedHandlder = $handlder;
                $patchedHandlder->setFilenameFormat(
                    '{date}-{filename}',
                    'Y-m-d'
                );
                $this->logger->popHandler();
                $this->logger->pushHandler($patchedHandlder);
            }
        }
        unset($handlers);
        //\Monolog\ErrorHandler::register($this->logger);
    }

    /**
     * return monolog logger with handlers set
     *
     * @return Logger
     */
    public function getLogger(): Logger
    {
        return $this->logger;
    }



    /**
     * set app root path
     *
     */
    protected function setPath(string $path)
    {
        $this->path = $path;
    }

    /**
     * return kernel run path
     *
     * @return string
     */
    protected function getPath(): string
    {
        return $this->path;
    }

    /**
     * set kernel error
     *
     */
    protected function setError(bool $error)
    {
        $this->error = $error;
    }

    /**
     * return kernel error
     *
     */
    protected function getError(): bool
    {
        return $this->error;
    }

    /**
     * return kernel error message
     *
     */
    protected function getErrorMsg(): string
    {
        return $this->errorMsg;
    }
}
