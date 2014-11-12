<?php

// Database Connection Class extends PDO.

class Database extends PDO
{
    protected $options = [
            PDO::ATTR_CASE => PDO::CASE_NATURAL,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
            PDO::ATTR_STRINGIFY_FETCHES => false,
            PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    public function getOptions(array $config)
    {
        $options = array_get($config, 'options', array());
        return array_diff_keys($this->options, $options) + $options;
    }
    
    /**
     * Create PDO connection.
     * @param string $dsn
     * @param array $config
     * @param array $options
     * @return PDO
     */
    public function createConnection($dsn, array $config, array $options)
    {
        $username = array_get($config, 'username');
        $password = array_get($config, 'password');
        return new PDO($dsn, $username, $password, $options);
    }
    
    /**
     * Get the default attribute options.
     * @return array
     */
    public function getDefaultOptions()
    {
        return $this->options;
    }
     
    /**
     * Set default PDO connection options.
     * @param array $options
     * @return void
     */
    public function setDefaultOptions(array $options)
    {
        $this->options = $options;
    }
}

// Database Interface

interface DatabaseInterface
{
    public function connect(array $config);
}

// Database Factory

class DatabaseFactory extends Database
{
    
    protected $container;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    public function make(array $config, $name = null)
    {
        $config = $this->parseConfig($config, $name);
        if (isset($config['read']))
        {
            return $this->createReadWriteConnection($config);
        }
        return $this->createSingleConnection($config);
    }
    
    protected function createSingleConnection(array $config)
    {
        $pdo = $this->createConnection($config)->connect($config);
        return $this->createConnection($config['driver'], $pdo, $config['database'], $config['prefix'], $config);
    }
    
    protected function createWriteConnection(array $config)
    {
        $connection = $this->createSingleConnection($this->getWriteConfig($config));
        return $connection->setReadPdo($this->createReadPdo($config));
    }
    
    protected function createReadPdo(array $config)
    {
        $readConfig = $this->getReadConfig($config);
        return $this->createConnector($readConfig)->connect($readConfig);
    }

    protected function getReadConig(array $config)
    {
        $readConfig = $this->getReadWriteConfig($config, 'read');
        return $this->mergeReadWriteConfig($config, $readConfig);
    }
    
    protected function getWriteConfigf(array $config)
    {
        $writeConfig = $this->getReadWriteConfig($config, 'write');
        return $this->mergeReadWriteConfig($config, $writeConfig);
    }

    protected function getReadWriteConfig(array $config, $type)
    {
        if (isset($config[$type][0]))
        {
            return $config[$type][array_rand($config[$type])];
        }
        return $config[$type];
    }
    
    protected function mergeReadWriteConfig(array $config, array $merge)
    {
        return array_except(array_merge($config, $merge), ['read', 'write']);
    }
    
    protected function parseConfig(array $config, $name)
    {
        return array_add(array_add($config, 'prefix', ''), 'name', $name);
    }

    public function createConnector(array $config)
    {
        if (!isset($config['driver']))
        {
            throw new \InvalidArgumentException('A driver must be specified.');
        }
        if ($this->container->bound($key = "db.connector.{$config['driver']}"))
        {
            return $this->container->make($key);
        }
        switch ($config['driver'])
        {
            case 'mysql':
                return new MysqlConnector;
        }
        throw new \InvalidArgument\Exception('Unsupported driver [{'.$config['driver'].'}]');
    }
    
    /**
     * Note: dbh handler $connection should be converted to $dbh, if needed.
     */
    protected function createConnection($driver, PDO $connection, $database, $prefix, = '', array $config = array())
    {
        if ($this->container->bound($key = "db.connection.{$driver}"))
        {
            return $this->container->make($key, [$connection, $database, $prefix, $config]);
        }
        switch ($driver)
        {
            case 'mysql':
                return new MysqlConnection($connection, $database, $prefix, $config);
        }
        throw new \InvalidArgumentException('Unsupported driver ['.$driver.']');
    }
}

// Mysql Database Connector

class MysqlDatabase extends Database implements DatabaseInterface
{
    /**
     * Establish a database connection.
     * @param array $config
     */
    public function connect(array $config)
    {
        $dsn = $this->getDsn($config);
        $options = $this->getOptions($config);
        $dbh = $this->createConnection($dsn, $config, $options); // Database handler
        if (isset($config['unix_socket']))
        {
            $connection->exec("use {$config['database']};");
        }
        $collation = $config['collation'];
        
        // Setting the 'names' (charset) and 'collation' on connection.
        $charset = $config['charset'];
        $names = "set names '$charset'" . (!is_null($collation) ? "collate '$collation'" : '');
        $dbh->prepare($names)->execute();
        
        // Strict mode set
        if (isset($config['strict']) && $config['strict'])
        {
            $dbh->prepare('SET session sql_mode = "STRICT_ALL_TABLES"')->execute();
        }
        return $dbh;
    }
    
    /**
     * Create DSN string from configuration. (socket or host)
     * @param array $config
     * @return string
     */
    protected function getDsn(array $config)
    {
        return $this->configHasSocket($config) ? $this->getSocketDsn($config) : $this->getHostDsn($config);
    }
    
    /**
     * Determine if configuration array has a UNIX socket value.
     * @param array $config
     * @return bool
     */
    protected function configHasSocket(array $config)
    {
        return isset($config['unix_socket']) && !empty($config['unix_socket']);
    }
    
    /**
     * Get the DSN string for a socket configuration.
     * @param array $config
     * @return string
     */
    protected function getSocketDsn(array $config)
    {
        extract($config);
        return "mysql:unix_socket={$config['unix_socket']};dbname={$database}";
    }
    
    protected function getHostDsn(array $config)
    {
        extract($config);
        return isset($config['port'])
            ? "mysql:host={$host};port={$port};dbname={$database}"
            : "mysql:host={$host};dbname={$database}";
    }
}

