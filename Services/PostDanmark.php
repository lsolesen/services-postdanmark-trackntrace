<?php
/**
 * http://www.postdanmark.dk/contentfull.dk?content=/cms/da-dk/kontaktos/index.htm&menufile=/cms/da-dk/menufiles/kontaktos.xml&lang=dk
 */

require_once 'HTTP/Request.php';
require_once 'HTMLPurifier.php';

class Services_PostDanmark
{
    private $number;

    public function __construct($number)
    {
        $this->number = $number;
    }

    public function request()
    {
        $req = new HTTP_Request('http://www.postdanmark.dk/tracktrace/TrackTracePrint.do?i_stregkode=' . $this->number);

        if (!PEAR::isError($req->sendRequest())) {
            if (!$response = $req->getResponseBody()) {
                throw new Exception('invalid request');
            }
            return $response;
        }

        throw new Exception('could not perform request');
    }

    static public function cleanupResponse($response)
    {
        $response = preg_replace("@<script[^>]*?>.*?</script>@si", '', $response);

        $config = HTMLPurifier_Config::createDefault();
        //$config->set('HTML', 'TidyLevel', 'heavy');

        $purifier = new HTMLPurifier($config);
        return $purifier->purify($response);
    }

    public function query()
    {
        $response = $this->request();
        $response = $this->cleanupResponse($response);

        $phpobject = simplexml_load_string($response);

        $i = 0;
        $key = '';
        $array = array();
        foreach ($phpobject->tr->td[1]->div->table->tbody as $object) {
            for ($j = 0, $max = count($object->tr->td); $j < $max; $j++) {
                if ($j == 0) {
                    $key = 'date';
                } elseif ($j == 1) {
                    $key = 'time';
                } elseif ($j == 2) {
                    $key = 'text';
                }

                $array[$i][$key] = $object->tr->td[$j];

            }
            $i++;
        }

        return new ArrayObject($array);
    }
}
?>