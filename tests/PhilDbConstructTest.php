<?php

namespace PhilDb\Tests;

use PhilDb\PhilDb;
use PhilDb\PhilDb_Exception;

/**
 * Class to test the construction of the Phil_Db class
 *
 * @author  Phil Burton <phil@pgburton.com>
 */
class PhilDbConstructTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Check the constuctor works with a good config
     *
     * @author Phil Burton <phil@pgburton.com>
     */
    public function testValidConfig()
    {
        // Set up the config required
        $config = [
            "hostname"  => "hostname",
            "dbname"    => "dbname",
            "username"  => "username",
            "password"  => "password",
            "driver"    => "driver"
        ];

        // Create instance of the new class
        $db         = false;
        $message    = false;
        try {
            $db = new PhilDb($config);
        } catch (PhilDb_Exception $e) {
            $message = $e->getMessage();
        }
        $this->assertInstanceOf("PhilDb\PhilDb", $db, $message);
        PhilDb::$instance = false;
    }

    /**
     * Check the constuctor fails when passing an empty array
     *
     * @author Phil Burton <phil@pgburton.com>
     */
    public function testMissingParams()
    {
        $db     = false;
        $config = [];
        try {
            $db = new PhilDb($config);
        } catch (PhilDb_Exception $e) {
            $message = $e->getMessage();
        }
        $this->assertEquals("Parameter 'hostname' was not given or set", $message);
    }


    /**
     * Check the constuctor fails when passing an incorrect param
     *
     * @author Phil Burton <phil@pgburton.com>
     */
    public function testIncorrectParams()
    {
        $db     = false;
        $config = [
            'test' => 'test'
        ];
        try {
            $db = new PhilDb($config);
        } catch (PhilDb_Exception $e) {
            $message = $e->getMessage();
        }
        $this->assertEquals("Parameter 'test' does not exist", $message);
    }

    /**
     * Check the constuctor fails when passing correct params with an empty
     * string. And cehck one that just has whitespace
     *
     * @author Phil Burton <phil@pgburton.com>
     */
    public function testEmptyValue()
    {
        $db     = false;
        $config = [
            "hostname"  => "hostname",
            "dbname"    => "dbname",
            "username"  => "username",
            "password"  => "password",
            "driver"    => ""
        ];

        try {
            $db = new PhilDb($config);
        } catch (PhilDb_Exception $e) {
            $message = $e->getMessage();
        }
        $this->assertEquals("Parameter 'driver' does not have a value", $message);

        // Add some whitespace to the driver
        $config['driver']   = " ";
        $db                 = false;
        try {
            $db = new PhilDb($config);
        } catch (PhilDb_Exception $e) {
            $message = $e->getMessage();
        }
        $this->assertEquals("Parameter 'driver' does not have a value", $message);
    }
}
