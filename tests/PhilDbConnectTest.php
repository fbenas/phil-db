<?php

namespace PhilDb\Tests;

use PhilDb\PhilDb;
use PhilDb\PhilDbException;

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
class PhilDbConnectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test connecting to the database with good config options
     *
     * @author Phil Burton <phil@pgburton.com>
     */
    public function testValidConnectionConfig()
    {
        $config = [
            "hostname"  => "localhost",
            "dbname"    => "mysql",
            "username"  => "phildb",
            "password"  => "phildbpass"
        ];
        $message    = false;
        $phildb     = false;
        $statement  = false;
        $result     = false;
        try {
            $phildb = PhilDb::factory($config);
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
        $config = [
            "hostname"  => "localhost",
            "dbname"    => "mysql",
            "username"  => "phildb",
            "password"  => "phildbpass"
        ];
        $message    = false;
        try {
            $phildb = PhilDb::factory($config);
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
    //     $config = [
    //         "hostname"  => "badhostname",
    //         "dbname"    => "mysql",
    //         "username"  => "phildb",
    //         "password"  => "phildbpass"
    //     ];
    //     $message    = false;
    //     try {
    //         $phildb = \PhilDb\PhilDb::factory($config);
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
        $config = [
            "hostname"  => "localhost",
            "dbname"    => "baddatabase",
            "username"  => "phildb",
            "password"  => "phildbpass"
        ];
        $message    = false;
        try {
            $phildb = \PhilDb\PhilDb::factory($config);
            $pdo = $phildb->connect([]);
        } catch (\PhilDb\PhilDbException $e) {
            $message = $e->getMessage();
        }
        $this->assertEquals("Database 'baddatabase' not found", $message);
        \PhilDb\PhilDb::$instance = false;
    }

    /**
     * Test connecting to the database with a bad username
     *
     * @author Phil Burton <phil@pgburton.com>
     */
    public function testBadUsername()
    {
        $config = [
            "hostname"  => "localhost",
            "dbname"    => "mysql",
            "username"  => "badusername",
            "password"  => "phildbpass"
        ];
        $message    = false;
        try {
            $phildb = \PhilDb\PhilDb::factory($config);
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
        $config = [
            "hostname"  => "localhost",
            "dbname"    => "mysql",
            "username"  => "phildb",
            "password"  => "badpass"
        ];
        $message    = false;
        try {
            $phildb = \PhilDb\PhilDb::factory($config);
            $pdo = $phildb->connect([]);
        } catch (\PhilDb\PhilDbException $e) {
            $message = $e->getMessage();
        }
        $this->assertTrue(false != $message);
        \PhilDb\PhilDb::$instance = false;
    }
}