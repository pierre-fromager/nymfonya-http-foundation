<?php

namespace Nymfonya\Component\Http;

use Nymfonya\Component\Http\Response;
use Nymfonya\Component\Http\Headers;

class Kernel
{

    const PATH_CONFIG = '/../config/';

    use \Nymfonya\Component\Http\Reuse\TKernel;

    /**
     * instanciate
     *
     * @return Kernel
     */
    public function __construct(string $env, string $path)
    {
        $this->init($env, $path);
    }

    /**
     * set controller namespace
     *
     * @param string $ctrlNamespace
     * @return Kernel
     */
    public function setNameSpace(string $ctrlNamespace): Kernel
    {
        $this->spacename = $ctrlNamespace;
        return $this;
    }

    /**
     * retrieve kernel instance classname from container
     *
     * @return string
     */
    public function getBundleClassname():string
    {
        return get_called_class();
    }

    /**
     * run app
     *
     * @param array $routerGroups
     * @return Kernel
     */
    public function run(array $groups = []): Kernel
    {
        $routerGroups = (empty($groups))
            ? $this->router->compile()
            : $groups;
        if ($routerGroups) {
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
     * @return Kernel
     */
    public function send(): Kernel
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
