<?php

namespace PhilDb\Tests;

use PhilDb\PhilDbConfig;

/**
 * Abstract for tests
 *
 * @author  Phil Burton <phil@pgburton.com>
 */
class PhilDbTestBase extends \PHPUnit_Framework_TestCase
{
    private $config;

    public function __construct()
    {
        parent::__construct();
        $this->config = new \PhilDb\PhilDBConfig("mysql", "phildb" , "phildbpass");
    }

    /**
     * Get a copy of the config
     *
     * @return this class
     * @author Phil Burton <phil@dpgburtoncom>
     */
    protected function getGoodConfig()
    {
        return $this->config;
    }

    /**
     * Get a copy of the config
     *
     * @return this class
     * @author Phil Burton <phil@pgburton.com>
     */
    protected function getBadDbnameConfig()
    {
        $this->config->dbname = 'bad-dbname';
        return $this->config;
    }

    /**
     * Get a copy of the config
     *
     * @return this class
     * @author Phil Burton <phil@pgburton.com>
     */
    protected function getBadUsernameConfig()
    {
        $this->config->username = 'bad-username';
        return $this->config;
    }

    /**
     * Get a copy of the config
     *
     * @return this class
     * @author Phil Burton <phil@pgburton.com>
     */
    protected function getBadPasswordConfig()
    {
        $this->config->password = 'bad-password';
        return $this->config;
    }

    /**
     * Get a copy of the config
     *
     * @return this class
     * @author Phil Burton <phil@pgburton.com>
     */
    protected function getBadHostnameConfig()
    {
        $this->config->hostname = 'bad-hostname';
        return $this->config;
    }

    /**
     * Get a copy of the config
     *
     * @return this class
     * @author Phil Burton <phil@pgburton.com>
     */
    protected function getBadDriverConfig()
    {
        $this->config->driver = 'bad-driver';
        return $this->config;
    }

    /**
     * Get a copy of the config
     *
     * @return this class
     * @author Phil Burton <phil@pgburton.com>
     */
    protected function getUnsupportedDriverConfig()
    {
        $this->config->driver = 'sqlite';
        return $this->config;
    }
}