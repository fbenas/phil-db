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
     * Set the pdo errormode
     * @var string
     */
    protected $errormode = "ERRMODE_EXCEPTION";

    /**
     * Database driver connection
     * @var mixed
     */
    public static $instance = false;

    /**
     * PDO connection
     * @var mixed
     */
    public $connection = false;
    /**
     * Additional DSN options
     * @var boolean
     */
    protected $options = false;

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
            // Set the value (ensure lower case)
            $this->$key = strtolower($option);
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
     * Curerntly only mysql is supported and that's the default
     *
     * @param  string $driver pdo driver type
     * @return PhilDb instance of this class
     * @author Phil Burton <phil@pgburton.com>
     * @throws PhilDb_Exception
     */
    public static function factory(array $config, $driver = "mysql")
    {
        if (self::$instance != false) {
            throw new PhilDb_Exception("Instance already exists");
        }
        // lowercase the driver
        $driver = strtolower($driver);

        // Get the available drivers
        $available = \PDO::getAvailableDrivers();

        if (!in_array($driver, $available)) {
            throw new PhilDb_Exception("Invalid database driver");
        }

        if (!in_array($driver, self::$supportedDrivers)) {
            throw new PhilDb_Exception("Unsupported database driver");
        }
        self::$instance = new self(array_merge(['driver' => $driver], $config));
        return self::$instance;
    }

    /**
     * Connect to the database, if it exists
     *
     * @param  array  $options additional dsn options
     * @return
     * @author Phil Burton <phil@pgburton.com>
     * @throws
     */
    public function connect(array $options)
    {
        if ($this->connection != false) {
            throw new PhilDb_Exception("Connection already exists");
        }
        $this->options = $options;

        try {
            $this->connection = new \PDO($this->buildDsn(), $this->username, $this->password);
            // Set the error mode
            $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::$this->errormode);
        } catch (\PDOException $e) {
            throw new PhilDb_Exception("Connetion failed:" . $e->getMessage());
        }
    }

    /**
     * Createa the pdo Data Source Name (dsn) from the driver/database name
     * and additional options
     *
     * @return string full dsn
     * @author Phil Burton <phil@pgburton.com>
     */
    protected function buildDsn()
    {
        $dsn = $this->driver . ":dbname=" . $this->dbname;

        foreach ($this->options as $key => $option) {
            $dsn .= $key . "=" . $value . ";";
        }
        return $dsn;
    }
}