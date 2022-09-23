<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 11-Apr-22
 * Time: 8:34 PM
 */

namespace App\Session;


use App\Interfaces\SessionInterface;

final class Session implements SessionInterface
{

    private bool $isStarted = false;


    /**
     * @return bool
     */
    public function isStarted(): bool
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return true;
        }

        return $this->isStarted;
    }

    public function start(): bool
    {
        if ($this->isStarted === true) {
            return true;
        }

        session_start();

        $this->isStarted = true;

        return true;

    }

    public function has(string $key) :bool
    {
        return array_key_exists($key, $_SESSION);
    }


    public function get(string $key)
    {
        if ($this->has($key)) {
            return $_SESSION[$key];
        }

        return null;
    }

    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }




}