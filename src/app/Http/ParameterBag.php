<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 10-Apr-22
 * Time: 3:15 PM
 */

namespace App\Http;


class ParameterBag
{

    /**
     * @var string
     */
    private $method;


    public function __construct(string $method)
    {
        $this->method = $method;
    }

    /**
     * @param string $string
     * @return mixed
     */
    public function get(string $string)
    {
        if ($this->method === 'query') {
            return $_GET[$string] ?? null;
        }

        if ($this->method === 'server') {

            return $_SERVER[$string] ?? null;
        }

        if ($this->method === 'request') {
            return $_POST[$string] ?? null;
        }

        if ($this->method === 'cookie') {
            return $_COOKIE[$string] ?? null;
        }


        return null;
    }

    public function all(): ?array
    {
        if ($this->method === 'query') {

            $newRequest = new Request();

            parse_str($newRequest->server->get('QUERY_STRING'),$arr);

            $allData = array();

            foreach ($arr as $contents) {
                foreach ($contents as $key => $content ) {
                    $allData[$key] = $content;
                }
            }

            return $allData;
        }

        return null;
    }

    public function getContent(): ?array
    {
        if ($this->method === 'request') {
            $data = file_get_contents('php://input', false);

            $newData = array();

            $allData = array();

            parse_str($data, $newData);

            foreach ($newData as $contents) {
                foreach ($contents as $key => $content ) {
                    $allData[$key] = $content;
                }
            }

            return $allData;
        }

        return null;
    }

    public function setCookie(string $name, string $value , array $options = []) :void
    {
        if ($this->method === 'cookie') {
            setcookie($name, $value, $options);
        }

    }





}