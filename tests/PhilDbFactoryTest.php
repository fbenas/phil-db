<?php

namespace PhilDb\Tests;

use PhilDb\PhilDb;
use PhilDb\PhilDbException;

/**
 * Class to test the factory creation of PhilDb Objects
 *
 * NOTE: This Class assumes that the sqlite and mysql drivers for PDO
 * are available
 *
 * @author  Phil Burton <phil@pgburton.com>
 */
class PhilDbFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Config array
     * @var array
     */
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
        } catch (\PhilDb\PhilDbException $e) {
            $message = $e->getMessage();
        }
        $this->assertInstanceOf("PhilDb\PhilDb", $db, $message);
        PhilDb::$instance = false;
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
        } catch (\PhilDb\PhilDbException $e) {
            $message = $e->getMessage();
        }
        $this->assertInstanceOf("PhilDb\PhilDb", $db, $message);
        PhilDb::$instance = false;
    }

    /**
     * Create insetance once, then try again
     *
     * @author Phil Burton <phil@pgburton.com>
     */
    public function testDriverInstanceExists()
    {
        $message    = false;
        $db         = false;
        try {
            $db = PhilDb::factory($this->config);
            $db = PhilDb::factory($this->config);
        } catch (\PhilDb\PhilDbException $e) {
            $message = $e->getMessage();
        }
        $this->assertEquals("Instance already exists", $message);
        PhilDb::$instance = false;
    }

    /**
     * Check the factory throws a \PhilDb\PhilDbException when trying to create
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
        } catch (\PhilDb\PhilDbException $e) {
            $message = $e->getMessage();
        }
        $this->assertEquals("Invalid database driver", $message);
        PhilDb::$instance = false;
    }

    /**
     * Check the factory throws a \PhilDb\PhilDbException when trying to create
     * an instance of the PhilDb class with an unsupoorted driver
     *
     * NOTE: This test will fail if sqlite is not a supported driver
     * run PDO::getAvailableDrivers() to see supported drivers
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
        } catch (\PhilDb\PhilDbException $e) {
            $message = $e->getMessage();
        }
        $this->assertEquals("Unsupported database driver", $message);
        PhilDb::$instance = false;
    }
}