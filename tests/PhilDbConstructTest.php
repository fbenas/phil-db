<?php

namespace PhilDb\Tests;

require __DIR__ . '/../vendor/autoload.php';

use PhilDb\PhilDb;
use PhilDb\PhilDb_Exception;
/**
 * Class to test the construction of the Phil_Db class
 * @author  Phil Burton <phil@pgburton.com>
 */
class PhilDbConstructTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Check the constuctor works with a good config
     *
     * @author Phil Burton <phil@pgburton.com>
     */
    public function testGoodConfig()
    {
        // Set up the config required
        $config = [
            "hostname" =>"hostname",
            "dbname" =>"dbname",
            "username" =>"username",
            "password" =>"password",
            "driver" =>"driver"
        ];

        // Create instance of the new class
        $db = false;
        $message = false;
        try {
            $db = new PhilDb($config);
        } catch (PhilDb_Exception $e) {
            $message = $e->getMessage();
        }
        $this->assertInstanceOf("PhilDb\PhilDb", $db, $message);
    }
}