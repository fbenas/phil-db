<?php

namespace PhilDb\Tests;

use PhilDb\PhilDb;
use PhilDb\PhilDbException;

/**
 * Class to test the prepared statements of the Phil_Db class
 *
 * NOTE: This test assumes that the database 'mysql' exists
 *       at a host 'localhost' with a username of 'phildb'
 *       having access to that database with pass 'phildbpass'
 *
 * TESTS WILL FAIL IF THIS IS NOT CONFIGURED CORRECTLY
 *
 * @author  Phil Burton <phil@pgburton.com>
 */
class PhilDbPrepareTest extends PhilDbTestBase
{
    /**
     * Run a prepare statement with no params
     *
     * @author Phil Burton <phil@pgburton.com>
     */
    public function testNoParamPrepare()
    {
        $message    = false;
        $phildb     = false;
        $statement  = false;
        try {
            $phildb = PhilDb::factory($this->getGoodConfig());
            $phildb->connect([]);
            $statement = $phildb->prepare("show tables", []);
            $statement->execute();
        } catch (\PhilDb\PhilDbException $e) {
            $message = $e->getMessage();
        }
        $this->assertTrue($message == false);
        $this->assertTrue(is_array($statement->fetch()));
        \PhilDb\PhilDb::$instance = false;
    }

    /**
     * Run a prepare statement with a param value passed in
     *
     * @author Phil Burton <phil@d3r.com>
     */
    public function testParamPrepare()
    {
        $message    = false;
        $phildb     = false;
        $statement  = false;
        try {
            $phildb = PhilDb::factory($this->getGoodConfig());
            $phildb->connect([]);
            $statement = $phildb->prepare("select * from user where host = :host", ["host" => "localhost"]);
            $statement->execute();
        } catch (\PhilDb\PhilDbException $e) {
            $message = $e->getMessage();
        }
        $this->assertTrue($message == false);
        $this->assertTrue(is_array($statement->fetch()));
        \PhilDb\PhilDb::$instance = false;
    }

    /**
     * Run a prepare statement with a missing param
     *
     * @author Phil Burton <phil@d3r.com>
     */
    public function testMissingParamPrepare()
    {
        $message    = false;
        $phildb     = false;
        $statement  = false;
        try {
            $phildb = PhilDb::factory($this->getGoodConfig());
            $phildb->connect([]);
            $statement = $phildb->prepare("select * from user where host = :host");
            $statement->execute();
        } catch (\PhilDb\PhilDbException $e) {
            $message = $e->getMessage();
        }
        $this->assertTrue($message != false);
        \PhilDb\PhilDb::$instance = false;
    }

    /**
     * Run a prepare statement with a missing param
     *
     * @author Phil Burton <phil@d3r.com>
     */
    public function testExecuteLastPrepare()
    {
        $message    = false;
        $phildb     = false;
        $statement  = false;
        try {
            $phildb = PhilDb::factory($this->getGoodConfig());
            $phildb->connect([]);
            $statement = $phildb->prepare("select * from user where host = 'localhost'");
            $statement->execute();
            $statement2 = $phildb->executeLast();
        } catch (\PhilDb\PhilDbException $e) {
            $message = $e->getMessage();
        }
        $this->assertTrue($message == false, $message);
        $this->assertInstanceOf("\PDOStatement", $statement2, $message);
        \PhilDb\PhilDb::$instance = false;
    }
}