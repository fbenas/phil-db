<?php

namespace PhilDb;

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
    protected static $supportedDrivers = ['mysql'];

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
     * @var mixed
     */
    protected $options = false;

    /**
     * The last statement prepared
     * @var boolean
     */
    protected $statement = false;

    /**
     * Constructer for creating instances of the Phil Db class
     * Use an array to set the config options
     * Throwing Phil_Db_Exception if important information is missing
     *
     * @param  array $config array of config options
     * @throws PhilDbException
     * @author Phil Burton <phil@pgburton.com>
     */
    public function __construct(array $config)
    {
        foreach ($config as $key => $option) {
            // Check the option's key is a valid variable on the class
            if (!property_exists($this, $key)) {
                throw new PhilDbException("Parameter '" . $key . "' does not exist");
            }
            // trim the option's value and check it's not empty/null
            if (!isset($option) || empty(trim($option))) {
                throw new PhilDbException("Parameter '" . $key . "' does not have a value");
            }
            // Set the value (ensure lower case)
            $this->$key = strtolower($option);
        }

        // Check we've set everything
        $important = ["hostname", "dbname", "username", "password", "driver"];
        foreach ($important as $i) {
            if (!isset($i) || empty($this->$i)) {
                throw new PhilDbException("Parameter '" . $i . "' was not given or set");
            }
        }
    }

    /**
     * Static factory for creating an instance of the PhilDb class
     * Curerntly only mysql is supported and that's the default
     *
     * @param  array  $config
     * @param  string $driver
     * @return PhilDb instance of this class
     * @author Phil Burton <phil@pgburton.com>
     * @throws PhilDbException
     */
    public static function factory(array $config, $driver = "mysql")
    {
        if (self::$instance != false) {
            throw new PhilDbException("Instance already exists");
        }
        // lowercase the driver
        $driver = strtolower($driver);

        // Get the available drivers
        $available = \PDO::getAvailableDrivers();

        if (!in_array($driver, $available)) {
            throw new PhilDbException("Invalid database driver");
        }

        if (!in_array($driver, self::$supportedDrivers)) {
            throw new PhilDbException("Unsupported database driver");
        }
        self::$instance = new self(array_merge(['driver' => $driver], $config));
        return self::$instance;
    }

    /**
     * Connect to the database, if it exists
     *
     * @param  array  $options additional dsn options
     * @return PDO A pdo connection
     * @author Phil Burton <phil@pgburton.com>
     * @throws PhilDbException
     */
    public function connect(array $options)
    {
        if ($this->connection != false) {
            throw new PhilDbException("Connection already exists");
        }
        $this->options = $options;

        try {
            $this->connection = new \PDO($this->buildDsn(), $this->username, $this->password);
            // Set the error mode
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            if ($e->getCode() == 2002) {
                throw new PhilDbException("Failed connecting to host '" . $this->hostname . "'");
            } elseif ($e->getCode() == 1049) {
                throw new PhilDbException("Database '" . $this->dbname . "' not found");
            }
            throw new PhilDbException("Connetion failed: " . $e->getMessage());
        }

        return $this->connection;
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
        $dsn  = $this->driver . ":dbname=" . $this->dbname . ";";
        $dsn .= "host=" . $this->hostname . ";";
        foreach ($this->options as $key => $option) {
            $dsn .= $key . "=" . $option . ";";
        }
        return $dsn;
    }

    /**
     * Run a PDO prepare statement
     *
     * @param  string $sql
     * @param  array  $params
     * @return PDOStatement
     * @author Phil Burton <phil@pgburton.com>
     */
    public function prepare($sql, array $params)
    {
        try {
            $this->statement = $this->connection->prepare($sql);
            foreach ($params as $key => $param) {
                $this->statement->bindValue($key, $param);
            }
            $this->statement->execute();

            return $this->statement;
        } catch (\PDOException $e) {
            throw new PhilDbException("Prepare Error: " . $e->getMessage());
        }
    }
}