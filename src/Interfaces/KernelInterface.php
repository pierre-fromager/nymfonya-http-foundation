<?php

namespace Nymfonya\Component\Http\Interfaces;

interface KernelInterface
{
    /**
     * instanciate
     *
     * @param string $env
     * @param string $path
     * @return KernelInterface
     */
    public function __construct(string $env, string $path);

    /**
     * set controller namespace
     *
     * @param string $ctrlNamespace
     * @return KernelInterface
     */
    public function setNameSpace(string $ctrlNamespace): KernelInterface;

    /**
     * retrieve kernel instance classname from container
     *
     * @return string
     */
    public function getBundleClassname(): string;

    /**
     * run app
     *
     * @param array $groups
     * @return KernelInterface
     */
    public function run(array $groups = []): KernelInterface;

    /**
     * set controller action from router groups
     *
     * @param array $routerGrps
     * @return void
     */
    public function setAction(array $routerGrps);

    /**
     * dispatch response
     *
     * @return KernelInterface
     */
    public function send(): KernelInterface;

    /**
     * shutdown kernel
     *
     * @return void
     */
    public function shutdown(int $code = 0);
}
