<?php

namespace PhilDb\Tests;

use PhilDb\PhilDb;
use PhilDb\PhilDbException;
use PhilDb\PhilDbConfig;

/**
 * Class to test the construction of the Phil_Db class
 *
 * NOTE: This test assumes that the database 'mysql' exists
 *       at a host 'localhost' with a username of 'phildb'
 *       having access to that database with pass 'phildbpass'
 *
 * TESTS WILL FAIL IF THIS IS NOT CONFIGURED CORRECTLY
 *
 * @author  Phil Burton <phil@pgburton.com>
 */
class PhilDbConnectTest extends PhilDbTestBase
{
    /**
     * Test connecting to the database with good config options
     *
     * @author Phil Burton <phil@pgburton.com>
     */
    public function testValidConnectionConfig()
    {
        $message    = false;
        $phildb     = false;
        $statement  = false;
        $result     = false;
        try {
            $phildb = PhilDb::factory($this->getGoodConfig());
            $phildb->connect([]);
            $statement = $phildb->prepare('show tables');
            $statement->execute();
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
        } catch (\PhilDb\PhilDbException $e) {
            $message = $e->getMessage();
        }
        $this->assertInstanceOf("\PDOStatement", $statement, $message);
        $this->assertTrue(is_array($result), $message);
        \PhilDb\PhilDb::$instance = false;
    }

    /**
     * Test connecting to the database with a bad charset
     *
     * @author Phil Burton <phil@pgburton.com>
     */
    public function testCharsetConfig()
    {
        $message    = false;
        try {
            $phildb = PhilDb::factory($this->getGoodConfig());
            $pdo = $phildb->connect(["charset" => "incorrect"]);
        } catch (\PhilDb\PhilDbException $e) {
            $message = $e->getMessage();
        }
        // make sure the message was thrown correctly
        $this->assertTrue(false != $message);
        \PhilDb\PhilDb::$instance = false;
    }

    // /**
    //  * Test connecting to the database with a bad hostname
    //  *
    //  * @author Phil Burton <phil@pgburton.com>
    //  */
    // public function testBadHostnameFound()
    // {
    //     $message    = false;
    //     try {
    //         $phildb = \PhilDb\PhilDb::factory($this->getBadHostnameConfig());
    //         $pdo = $phildb->connect([]);
    //     } catch (\PhilDb\PhilDbException $e) {
    //         $message = $e->getMessage();
    //     }
    //     $this->assertEquals("Failed connecting to host 'badhostname'", $message);
    //     \PhilDb\PhilDb::$instance = false;
    // }

    /**
     * Test connecting to the database with a bad hostname
     *
     * @author Phil Burton <phil@pgburton.com>
     */
    public function testDbnameNotFound()
    {
        $message    = false;
        try {
            $phildb = \PhilDb\PhilDb::factory($this->getBadDbnameConfig());
            $pdo = $phildb->connect([]);
        } catch (\PhilDb\PhilDbException $e) {
            $message = $e->getMessage();
        }
        $this->assertEquals("Database 'bad-dbname' not found", $message);
        \PhilDb\PhilDb::$instance = false;
    }

    /**
     * Test connecting to the database with a bad username
     *
     * @author Phil Burton <phil@pgburton.com>
     */
    public function testBadUsername()
    {
        $message    = false;
        try {
            $phildb = \PhilDb\PhilDb::factory($this->getBadUsernameConfig());
            $pdo = $phildb->connect([]);
        } catch (\PhilDb\PhilDbException $e) {
            $message = $e->getMessage();
        }
        $this->assertTrue(false != $message);
        \PhilDb\PhilDb::$instance = false;
    }

    /**
     * Test connecting to the database with a bad password
     *
     * @author Phil Burton <phil@pgburton.com>
     */
    public function testBadPassword()
    {
        $message    = false;
        try {
            $phildb = \PhilDb\PhilDb::factory($this->getBadPasswordConfig());
            $pdo = $phildb->connect([]);
        } catch (\PhilDb\PhilDbException $e) {
            $message = $e->getMessage();
        }
        $this->assertTrue(false != $message);
        \PhilDb\PhilDb::$instance = false;
    }
}