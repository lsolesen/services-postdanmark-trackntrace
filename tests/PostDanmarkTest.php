<?php
/**
 * Test class
 *
 * Requires Sebastian Bergmann's PHPUnit
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
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../Services/PostDanmark.php';

/**
 * Test class
 *
 * @category  Services
 * @package   Services_PostDanmark
 * @author    Lars Olesen <lars@legestue.net>
 * @copyright 2007 Authors
 * @license   GPL http://www.opensource.org/licenses/gpl-license.php
 * @version   @package-version@
 * @link      http://public.intraface.dk
 */
class PostDanmarkTest extends PHPUnit_Framework_TestCase
{
    public function testConstruction()
    {
        $track = new Services_PostDanmark('xx');
        $this->assertTrue(is_object($track));
    }

    public function testValidTrackAndTraceNumberReturnsAnArrayObject()
    {
        $service = new Services_PostDanmark('TS123456789DK');
        $r = $service->query();
        $this->assertTrue(is_object($r));
        $this->assertEquals(get_class($r), 'ArrayObject');
    }

    public function testInValidTrackAndTraceNumberThrowsException()
    {
        $track = new Services_PostDanmark('xx');

        try {
            $r = $track->query();
        } catch (Exception $e) {
            return;
        }
        $this->fail('Should throw an exception');
    }
}
?>