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
class PhilDbFactoryTest extends PhilDbTestBase
{
    /**
     * Create insetance once, then try again
     *
     * @author Phil Burton <phil@pgburton.com>
     */
    public function testPhilDbInstanceExists()
    {
        $message    = false;
        $db         = false;
        try {
            $db = PhilDb::factory($this->getGoodConfig());
            $db = PhilDb::factory($this->getGoodConfig());
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
            $db = PhilDb::factory($this->getBadDriverConfig());
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
            $db = PhilDb::factory($this->getUnsupportedDriverConfig());
        } catch (\PhilDb\PhilDbException $e) {
            $message = $e->getMessage();
        }
        $this->assertEquals("Unsupported database driver", $message);
        PhilDb::$instance = false;
    }
}