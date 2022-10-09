<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 10-Apr-22
 * Time: 3:15 PM
 */

namespace App\Http;


use App\Utils\Utils;

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
    public function get(string $string): mixed
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

        if (($this->method === 'files') && !empty($_FILES[$string])) {
            $arrayOfFile = [];

            foreach ($_FILES[$string] as $key => $value) {
                $arrayOfFile[$key] = $value[$string];
            }
            return $arrayOfFile;
        }

        return null;
    }

    public function all(): ?array
    {
        if ($this->method === 'query') {

            $newRequest = new Request();

            parse_str($newRequest->server->get('QUERY_STRING'),$arr);

            $allData = array();

            foreach ($arr as $first => $contents) {
                if (is_array($contents)) {
                    foreach ($contents as $key => $content) {
                        $allData[$key] = $content;
                    }
                } else {
                    $allData[$first] = $contents;
                }
            }

            return $allData;
        }

        if ($this->method === 'files') {
            $arrayOfFiles = [];
            if (!empty($_FILES)) {
                foreach ($_FILES as $name => $file) {
                    foreach ($file as $key => $value) {
                        $arrayOfFiles[$name][$key] = $value[$name];
                    }
                }
            }
            return $arrayOfFiles;
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

            if (empty($data)) {
                $newData = $_POST;
            }

            foreach ($newData as $contents) {
                foreach ($contents as $key => $content ) {
                    $allData[$key] = $content;
                }
            }
            return $allData;
        }
        return null;
    }

    public function getJsonDataFromAjaxRequest()
    {
        if ($this->method === 'request') {
            $data = file_get_contents('php://input', false);
            if (Utils::checkIfJson($data)) {
               return json_decode($data, true);
            }
        }
        return null;
    }


    public function setCookie(string $name, string $value , array $options = []): void
    {
        if ($this->method === 'cookie') {
            setcookie($name, $value, $options);
        }
    }





}