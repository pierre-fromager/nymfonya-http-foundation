<?php

namespace Nymfonya\Component\Http;

use Nymfonya\Component\Http\Headers;
use Nymfonya\Component\Http\Interfaces\KernelInterface;
use Nymfonya\Component\Http\Response;
use Nymfonya\Component\Http\Interfaces\KernelEventsInterface;
use Nymfonya\Component\Pubsub\Dispatcher;
use Nymfonya\Component\Pubsub\Event;

class Kernel implements KernelInterface
{

    const PATH_CONFIG = '/../config/';

    use \Nymfonya\Component\Http\Reuse\TKernel;

    /**
     * instanciate
     *
     * @param string $env
     * @param string $path
     */
    public function __construct(string $env, string $path)
    {
        $this->init($env, $path);
        $this->dispatcher->publish(
            new Event(
                get_class($this), 
                KernelEventsInterface::EVENT_KERNEL_BOOT,
                $this
            )
        );
    }

    /**
     * set controller namespace
     *
     * @param string $ctrlNamespace
     * @return KernelInterface
     */
    public function setNameSpace(string $ctrlNamespace): KernelInterface
    {
        $this->spacename = $ctrlNamespace;
        return $this;
    }

    /**
     * retrieve kernel instance classname from container
     *
     * @return string
     */
    public function getBundleClassname(): string
    {
        return get_called_class();
    }

    /**
     * set pubsub dispatcher
     *
     * @param Dispatcher $dispatcher
     * @return KernelInterface
     */
    public function setDispatcher(Dispatcher $dispatcher = null): KernelInterface
    {
        if ($dispatcher instanceof Dispatcher) {
            $this->dispatcher = $dispatcher;
            return $this;
        }
        $this->dispatcher = new Dispatcher();
        return $this;
    }

    /**
     * run app
     *
     * @param array $groups
     * @return KernelInterface
     */
    public function run(array $groups = []): KernelInterface
    {
        $routerGroups = (empty($groups))
            ? $this->router->compile()
            : $groups;
        if (!empty($routerGroups)) {
            $this->setClassname($routerGroups);
            if (class_exists($this->className)) {
                $this->setController();
                $this->setReflector();
                $this->setActions();
                $this->setAction($routerGroups);
                //->setActionAnnotations();
                $this->setMiddleware();
            } else {
                $this->error = true;
                $this->errorCode = Response::HTTP_SERVICE_UNAVAILABLE;
            }
        }
        return $this;
    }

    /**
     * set controller action from router groups
     *
     * @param array $routerGrps
     * @return void
     */
    public function setAction(array $routerGrps)
    {
        $this->action = isset($routerGrps[1]) ? strtolower($routerGrps[1]) : '';
    }

    /**
     * dispatch response
     *
     * @return KernelInterface
     */
    public function send(): KernelInterface
    {
        if ($this->getError()) {
            $this->res
                ->setCode($this->errorCode)
                ->setContent([
                    Response::_ERROR => $this->error,
                    Response::_ERROR_CODE => $this->errorCode,
                    Response::_ERROR_MSG => $this->errorMsg,
                ])
                ->getHeaderManager()
                ->add(
                    Headers::CONTENT_TYPE,
                    'application/json; charset=utf-8'
                );
            $this->getLogger()->warning($this->errorMsg);
        } else {
            $this->getLogger()->debug('Response sent');
        }
        $this->getResponse()->send();
        return $this;
    }

    /**
     * shutdown kernel
     *
     * @return void
     */
    public function shutdown(int $code = 0)
    {
        throw new \Exception('shutdown', $code);
    }
}
