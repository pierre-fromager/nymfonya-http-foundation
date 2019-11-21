<?php

namespace Nymfonya\Component\Http\Interfaces;

interface SessionInterface
{

    /**
     * instanciate
     *
     */
    public function __construct();

    /**
     * start a session with a session name
     *
     * @param string $sessionName
     * @return SessionInterface
     */
    public function startSession(string $sessionName): SessionInterface;

    /**
     * set name value in session for a given key
     *
     * @param string $name
     * @param mixed $value
     * @param string $key
     * @return SessionInterface
     */
    public function setSession(string $name, $value, $key = ''): SessionInterface;

    /**
     * remove a session entry name for a given key
     *
     * @param string $name
     * @param string $key
     * @return SessionInterface
     */
    public function deleteSession(string $name, string $key = ''): SessionInterface;

    /**
     * return true if a session name/key entry exists
     *
     * @param string $name
     * @param string $key
     * @return boolean
     */
    public function hasSession(string $name, string $key = ''): bool;

    /**
     * return entry name/key session value
     *
     * @param string $name
     * @param string $key
     * @return mixed
     */
    public function getSession(string $name, string $key = '');
}
