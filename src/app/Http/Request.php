<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 10-Apr-22
 * Time: 3:09 PM
 */

namespace App\Http;


class Request
{
    public ParameterBag $request ;

    public ParameterBag $query ;

    public ParameterBag $server;

    public ParameterBag $cookie;

    public ParameterBag $files;

    protected static array $trustedProxies = [];

    private static int $trustedHeaderSet = -1;

    public const HEADER_FORWARDED = 0b00001; // When using RFC 7239
    public const HEADER_X_FORWARDED_FOR = 0b00010;
    public const HEADER_X_FORWARDED_HOST = 0b00100;
    public const HEADER_X_FORWARDED_PROTO = 0b01000;
    public const HEADER_X_FORWARDED_PORT = 0b10000;
    public const HEADER_X_FORWARDED_ALL = 0b11110; // All "X-Forwarded-*" headers
    public const HEADER_X_FORWARDED_AWS_ELB = 0b11010; // AWS ELB doesn't send X-Forwarded-Host

    protected static $trustedHostPatterns = [];

    protected static $trustedHosts = [];

    public function __construct()
    {
        $this->query = new ParameterBag('query');

        $this->request = new ParameterBag('request');

        $this->server = new ParameterBag('server');

        $this->cookie = new ParameterBag('cookie');

        $this->files = new ParameterBag('files');
    }

    public static function setTrustedProxies(array $proxies, int $trustedHeaderSet): void
    {
        self::$trustedProxies = array_reduce($proxies, function ($proxies, $proxy) {
            if ('REMOTE_ADDR' !== $proxy) {
                $proxies[] = $proxy;
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $proxies[] = $_SERVER['REMOTE_ADDR'];
            }

            return $proxies;
        }, []);
        self::$trustedHeaderSet = $trustedHeaderSet;
    }

    public static function setTrustedHosts(array $hostPatterns): void
    {
        self::$trustedHostPatterns = array_map(function ($hostPattern) {
            return sprintf('{%s}i', $hostPattern);
        }, $hostPatterns);
        // we need to reset trusted hosts on trusted host patterns change
        self::$trustedHosts = [];
    }

    public function isMethod()
    {
        return $this->server->get('REQUEST_METHOD');
    }

    public function getHost(): string
    {
        return 'http://'.$this->server->get('HTTP_HOST');
    }

    public function getRequestedUri()
    {
        return $this->server->get('REQUEST_URI');
    }



}