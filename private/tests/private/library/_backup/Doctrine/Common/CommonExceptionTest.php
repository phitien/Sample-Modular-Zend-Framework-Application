<?php

namespace Doctrine\Common;

require_once dirname(__FILE__) . '/../../../../../../library/_backup/Doctrine/Common/CommonException.php';

/**
 * Test class for CommonException.
 * Generated by PHPUnit on 2011-07-02 at 16:23:13.
 */
class CommonExceptionTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var CommonException
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
	$this->object = new CommonException;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
	
    }

}

?>
