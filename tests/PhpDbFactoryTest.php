<?php

namespace PhilDb\Tests;

require_once(__DIR__ . '/../vendor/autoload.php');

use PhilDb\PhilDb;
use PhilDb\PhilDb_Exception;

/**
 * Class to test the factory creation of PhilDb Objects
 *
 * @author  Phil Burton <phil@pgburton.com>
 */
class PhilDbFactoryTest extends \PHPUnit_Framework_TestCase
{
    // Test config array
    protected $config = [
        "hostname"  => "hostname",
        "dbname"    => "dbname",
        "username"  => "username",
        "password"  => "password"
    ];

    /**
     * Check the factory returns an object when using mysql as the driver
     *
     * @author Phil Burton <phil@pgburton.com>
     */
    public function testMysqlDriver()
    {
        $driver     = "mysql";
        $message    = false;
        $db         = false;
        try {
            $db = PhilDb::factory($this->config, $driver);
        } catch (\PhilDb\PhilDb_Exception $e) {
            $message = $e->getMessage();
        }
        $this->assertInstanceOf("PhilDb\PhilDb", $db, $message);
        PhilDb::$connection = false;
    }

    /**
     * Check the factory returns an object when passing no driver param
     *
     * @author Phil Burton <phil@pgburton.com>
     */
    public function testNoDriverParam()
    {
        $message    = false;
        $db         = false;
        try {
            $db = PhilDb::factory($this->config);
        } catch (\PhilDb\PhilDb_Exception $e) {
            $message = $e->getMessage();
        }
        $this->assertInstanceOf("PhilDb\PhilDb", $db, $message);
        PhilDb::$connection = false;
    }

    /**
     * Create connection once, then try again
     *
     * @author Phil Burton <phil@pgburton.com>
     */
    public function testDriverConnectionExists()
    {
        $message    = false;
        $db         = false;
        try {
            $db = PhilDb::factory($this->config);
            $db = PhilDb::factory($this->config);
        } catch (\PhilDb\PhilDb_Exception $e) {
            $message = $e->getMessage();
        }
        $this->assertEquals("Connection already exists", $message);
        PhilDb::$connection = false;
    }

    /**
     * Check the factory throws a \PhilDb\PhilDb_Exception when trying to create
     * an instance of the PhilDb class with an invalid driver
     *
     * @author Phil Burton <phil@pgburton.com>
     */
    public function testInvalidDriverParam()
    {
        $message    = false;
        $db         = false;
        $driver     = 'test';
        try {
            $db = PhilDb::factory($this->config, $driver);
        } catch (\PhilDb\PhilDb_Exception $e) {
            $message = $e->getMessage();
        }
        $this->assertEquals("Invalid database driver", $message);
        PhilDb::$connection = false;
    }

    /**
     * Check the factory throws a \PhilDb\PhilDb_Exception when trying to create
     * an instance of the PhilDb class with an unsupoorted driver
     *
     * @author Phil Burton <phil@pgburton.com>
     */
    public function testUnsupportedDriverParam()
    {
        $message    = false;
        $db         = false;
        $driver     = 'sqlite';
        try {
            $db = PhilDb::factory($this->config, $driver);
        } catch (\PhilDb\PhilDb_Exception $e) {
            $message = $e->getMessage();
        }
        $this->assertEquals("Unsupported database driver", $message);
        PhilDb::$connection = false;
    }
}