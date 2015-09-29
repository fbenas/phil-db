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
     * @param  PhilDbConfig $config array of config options
     * @throws PhilDbException
     * @author Phil Burton <phil@pgburton.com>
     */
    public function __construct(PhilDbConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Static factory for creating an instance of the PhilDb class
     * Curerntly only mysql is supported and that's the default
     *
     * @param  PhilDbConfig  $config
     * @return PhilDb instance of this class
     * @author Phil Burton <phil@pgburton.com>
     * @throws PhilDbException
     */
    public static function factory(PhilDbConfig $config)
    {
        if (self::$instance != false) {
            throw new PhilDbException("Instance already exists");
        }

        // Get the available drivers
        $available = \PDO::getAvailableDrivers();

        if (!in_array($config->driver, $available)) {
            throw new PhilDbException("Invalid database driver");
        }

        if (!in_array($config->driver, self::$supportedDrivers)) {
            throw new PhilDbException("Unsupported database driver");
        }
        self::$instance = new self($config);
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
            $this->connection = new \PDO($this->buildDsn(), $this->config->username, $this->config->password);
            // Set the error mode
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            if ($e->getCode() == 2002) {
                throw new PhilDbException("Failed connecting to host '" . $this->config->hostname . "'");
            } elseif ($e->getCode() == 1049) {
                throw new PhilDbException("Database '" . $this->config->dbname . "' not found");
            }
            throw new PhilDbException("Connetion failed: " . $e->getMessage());
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
        $dsn  = $this->config->driver . ":dbname=" . $this->config->dbname . ";";
        $dsn .= "host=" . $this->config->hostname . ";";
        foreach ($this->options as $key => $option) {
            $dsn .= $key . "=" . $option . ";";
        }
        return $dsn;
    }

    /**
     * Prepare and execute an sql statement with params injected
     *
     * @param  string $sql
     * @param  array  $params
     * @return PDOStatement
     * @author Phil Burton <phil@pgburton.com>
     */
    public function prepare($sql, $params = false)
    {
        if ($params === false) {
            $params = [];
        }
        if (!is_array($params)) {
            throw new PhilDbException("Second parameter for function 'prepare' must be an array or false");
        }
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

    /**
     * If there is a statement stored on the isntance, re run it.
     *
     * @return PhilDb
     * @author Phil Burton <phil@pgburton.com>
     * @throws PhilDBException
     */
    public function executeLast()
    {
        if (!$this->statement) {
            throw new PhilDbException("No statement to exectute");
        }
        $this->statement->execute();
        return $this->statement;
    }
}
