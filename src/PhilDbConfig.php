<?php

namespace PhilDb;

/**
 * A database wrapper for PDO database extension
 *
 * @author Phil Burton <phil@pgburton.com>
 */
class PhilDbConfig
{
    /**
     * hostname for the database server
     * @var string
     */
    public $hostname;

    /**
     * name of the database
     * @var string
     */
    public $dbname;

    /**
     * Username for the database
     * @var string
     */
    public $username;

    /**
     * password for the database
     * @var string
     */
    public $password;

    /**
     * Constuctor for the config class, if no option is given for hostname
     * localhost is assumed
     * Any not set will throw an Exception
     *
     * Yes this is using reflection
     * No, I do not know why I did that
     *
     * @param  string $dbname
     * @param  string $username
     * @param  string $password
     * @param  string $hostname
     * @param  string $driver
     * @author Phil Burton <phil@d3r.com>
     */
    public function __construct($dbname, $username, $password, $hostname = "localhost", $driver = "mysql")
    {
        $reflect = new \ReflectionMethod($this, "__construct");
        $error = [];
        $reflectParams = $reflect->getParameters();

        foreach ($reflectParams as $param) {
            $name = $param->name;
            if (!$name || empty(trim(${$name}))) {
                $error[] = "'" . $name . "'";
            }
            $this->{$name} = strtolower(trim(${$name}));
        }
        if (!empty($error)) {
            $message = "One or more required config variables missing: ";
            $message .= implode(", ", $error);
            throw new PhilDbException($message);
        }
    }
}
