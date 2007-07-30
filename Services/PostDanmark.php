<?php
/**
 * Communicates with PostDanmark Track'n'Trace
 *
 * http://www.postdanmark.dk/contentfull.dk?content=/cms/da-dk/kontaktos/index.htm&menufile=/cms/da-dk/menufiles/kontaktos.xml&lang=dk
 *
 * PHP version 5
 *
 * @category  Services
 * @package   Services_PostDanmark
 * @author    Lars Olesen <lars@legestue.net>
 * @copyright 2007 Authors
 * @license   GPL http://www.opensource.org/licenses/gpl-license.php
 * @version   @package-version@
 * @link      http://public.intraface.dk
 */
require_once 'HTTP/Request.php';
require_once 'HTMLPurifier.php';

/**
 * Communicates with PostDanmark Track'n'Trace
 *
 * http://www.postdanmark.dk/contentfull.dk?content=/cms/da-dk/kontaktos/index.htm&menufile=/cms/da-dk/menufiles/kontaktos.xml&lang=dk
 *
 * @category  Services
 * @package   Services_PostDanmark
 * @author    Lars Olesen <lars@legestue.net>
 * @copyright 2007 Authors
 * @license   GPL http://www.opensource.org/licenses/gpl-license.php
 * @version   @package-version@
 * @link      http://public.intraface.dk
 */
class Services_PostDanmark
{
    /**
     * @var integer
     */
    private $number;

    /**
     * @var string
     */
    private $url;

    /**
     * Constructor
     *
     * @param integer $number Track'n'Trace number
     * @param string  $url    Url at PostDanmark
     *
     * @return void
     */
    public function __construct($number, $url = 'http://www.postdanmark.dk/tracktrace/TrackTracePrint.do?i_stregkode=')
    {
        $this->number = $number;
        $this->url    = $url;

    }

    /**
     * Request information
     *
     * @return object
     */
    public function request()
    {
        $req = new HTTP_Request($this->url . rawurlencode($this->number));

        if (!PEAR::isError($req->sendRequest())) {
            if (!$response = $req->getResponseBody()) {
                throw new Exception('invalid request');
            }
            return $response;
        }

        throw new Exception('could not perform request');
    }

    /**
     * Cleans up the response so it can be worked with
     *
     * @param string  $response The response from PostDanmark Webservice
     *
     * @return string
     */
    static public function cleanupResponse($response)
    {
        $response = preg_replace("@<script[^>]*?>.*?</script>@si", '', $response);

        $config = HTMLPurifier_Config::createDefault();
        //$config->set('HTML', 'TidyLevel', 'heavy');

        $purifier = new HTMLPurifier($config);
        return $purifier->purify($response);
    }

    /**
     * Queries the PostDanmark service
     *
     * @return ArrayObject
     */
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