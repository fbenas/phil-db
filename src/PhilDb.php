<?php

namespace PhilDb;

use PhilDb\PhilDb_Exception;

/**
 * A database wrapper for PDO database extension
 *
 * @author Phil Burton <phil@pgburton.com>
 */
class PhilDb
{
    /**
     * hostname for the database server
     * @var string
     */
    protected $hostname;

    /**
     * name of the database
     * @var string
     */
    protected $dbname;

    /**
     * Username for the database
     * @var string
     */
    protected $username;

    /**
     * password for the database
     * @var string
     */
    protected $password;

    /**
     * name of database driver to use
     * @var string
     */
    protected $driver;

    /**
     * An array of drivers this warapper supports
     * @var string[]
     */
    protected static $supportedDrivers = [
        'mysql'
    ];

    /**
     * Database driver connection
     * @var mixed
     */
    public static $connection = false;

    /**
     * Constructer for creating instances of the Phil Db class
     * Use an array to set the config options
     * Throwing Phil_Db_Exception if important information is missing
     *
     * @param  array $config array of config options
     * @throws Phil_Db_Exception
     * @author Phil Burton <phil@pgburton.com>
     */
    public function __construct(array $config)
    {
        foreach ($config as $key => $option) {
            // Check the option's key is a valid variable on the class
            if (!property_exists($this, $key)) {
                throw new PhilDb_Exception("Parameter '" . $key . "' does not exist");
            }
            // trim the option's value and check it's not empty/null
            if (!isset($option) || empty(trim($option))) {
                throw new PhilDb_Exception("Parameter '" . $key . "' does not have a value");
            }
            // Set the value
            $this->$key = $option;
        }

        // Check we've set everything
        $important = ["hostname", "dbname", "username", "password", "driver"];
        foreach ($important as $i) {
            if (!isset($i) || empty($this->$i)) {
                throw new PhilDb_Exception("Parameter '" . $i . "' was not given or set");
            }
        }
    }

    /**
     * Static factory for creating an instance of the PhilDb class
     * One parameter to choose the database driver.
     * Curerntly only mysql is supported and that's the default
     *
     * @param  string $driver pdo driver type
     * @return PhilDb instance of this class
     * @author Phil Burton <phil@pgburton.com>
     * @throws PhilDb_Exception
     */
    public static function factory(array $config, $driver = "mysql")
    {
        if (self::$connection != false) {
            throw new PhilDb_Exception("Connection already exists");
        }
        // Get the available drivers
        $available = \PDO::getAvailableDrivers();

        if (!in_array($driver, $available)) {
            throw new PhilDb_Exception("Invalid database driver");
        }

        if (!in_array($driver, self::$supportedDrivers)) {
            throw new PhilDb_Exception("Unsupported database driver");
        }
        self::$connection = new self(array_merge(['driver' => $driver], $config));
        return self::$connection;
    }
}