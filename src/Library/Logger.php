<?php

namespace Interart\Flywork\Library;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Log Management class.
 *
 * @copyright   2019 Silvio Delgado
 * @author      Silvio Delgado - silviomdelgado@gmail.com
 *
 * @version     1.2
 */
final class Logger extends AbstractLogger implements LoggerInterface
{
    const STORAGE_TYPE_FILE = 1;
    const STORAGE_TYPE_DB = 2;

    private $db;
    private $storage_type;
    private $log_path = '';
    private $db_settings;

    /**
     * Default constructor.
     *
     * @param int $storageType Class static constants
     */
    public function __construct(int $storageType = self::STORAGE_TYPE_FILE, \PDO $pdo = null)
    {
        if (!in_array($storageType, [self::STORAGE_TYPE_FILE, self::STORAGE_TYPE_DB])) {
            throw new \InvalidArgumentException('Unknown storage type');
        }
        $this->storage_type = $storageType;
        $this->db = $pdo;
    }

    /**
     * Set path where to save log files.
     *
     * @param string $path
     *
     * @return void
     */
    public function setPath(string $path)
    {
        $this->log_path = rtrim($path, '/') . DIRECTORY_SEPARATOR;
    }

    /**
     * Set Database configuration.
     *
     * @param mixed $dbSettings Could be an array (with database vars), a string with DSN or a PDO object.
     *
     * @return void
     */
    public function setDatabase($dbSettings)
    {
        if ($this->storage_type != self::STORAGE_TYPE_DB) {
            throw new \BadMethodCallException('Selected storage type mismatch');
        }

        $this->db_settings = $dbSettings;
    }

    /**
     * Save log to file (appending message).
     *
     * @param string $filename
     * @param string $message
     *
     * @return void
     */
    private function appendToFile($filename, $message)
    {
        \file_put_contents($filename, $message, FILE_APPEND);
    }

    /**
     * Create DSN to connect to Database.
     *
     * @param string $dbType
     * @param string $dbHost
     * @param string $dbName
     * @param string $charset [Optional] Default: 'utf-8'
     *
     * @return void
     */
    private function createDsn(string $dbType, string $dbHost, string $dbName, string $charset = 'utf-8')
    {
        switch ($dbType) {
            // mysql
            case 'mysql':
                // postgresql
            case 'pgsql':return "{$dbType}:dbname={$dbName};host={$dbHost}";
                // ms sql server
            case 'sqlsrv':return "sqlsrv:Server={$dbHost};Database={$dbName}";
                // oracle
            case 'oci':return "oci:dbname={$dbHost}/{$dbName};charset={$charset}";
        }
    }

    /**
     * Connect to Database.
     *
     * @return void
     */
    private function connect()
    {
        if (empty($this->db_settings)) {
            throw new \Exception('DB Settings was not initialized.');
        }

        if (is_a($this->db_settings, '\PDO')) {
            $this->db = $this->db_settings;

            return $this->db;
        }

        if (!is_array($this->db_settings)) {
            throw new \Exception('You must set database settings before save log entry');
        }

        $dsn = $this->createDsn($this->db_settings['type'], $this->db_settings['host'], $this->db_settings['name'], $this->db_settings['charset']);

        try {
            $db = new \PDO($dsn, $this->db_settings['user'], $this->db_settings['pass'], [
                \PDO::ATTR_ERRMODE                  => \PDO::ERRMODE_EXCEPTION,
                \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            ]);

            return $db;
        } catch (\PDOException $ex) {
            throw new \Exception('Falha ao conectar: ' . $ex->getMessage());
        }
    }

    /**
     * Save log do Database.
     *
     * @param string $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    private function saveToDb($level, $message, array $context = [])
    {
        $this->db = $this->db ?? $this->connect();
        if (!$this->db) {
            throw new \Exception('Cannot initialize DB Log handler');
        }

        $res = $this->db->prepare('INSERT INTO log_entry (level, message) VALUES (:level, :message)');
        $res->bindParam(':level', $level);
        $res->bindParam(':message', $message);
        $res->execute();
    }

    private function validateSettings()
    {
        if (!in_array($this->storage_type, [self::STORAGE_TYPE_FILE, self::STORAGE_TYPE_DB])) {
            throw new \Exception('Storage type wasn\'t properly configured.');
        }

        if ($this->storage_type == self::STORAGE_TYPE_FILE) {
            if (empty($this->log_path)) {
                throw new \Exception('You must set log path before save log entry.');
            }
            if (!is_writable($this->log_path)) {
                throw new \Exception("Directory '{$this->log_path}' is not writable.");
            }
        }

        if ($this->storage_type == self::STORAGE_TYPE_DB && empty($this->db) && empty($this->db_settings)) {
            throw new \Exception('You must set database settings before save log entry.');
        }
    }

    /**
     * Save log to defined level.
     *
     * @param string $level
     * @param mixed $message
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        $this->validateSettings();

        if (!in_array($level, [
            LogLevel::EMERGENCY,
            LogLevel::ALERT,
            LogLevel::CRITICAL,
            LogLevel::ERROR,
            LogLevel::WARNING,
            LogLevel::NOTICE,
            LogLevel::DEBUG,
            LogLevel::INFO,
        ])) {
            throw new \Psr\Log\InvalidArgumentException('Invalid log level');
        }

        if (empty($message)) {
            throw new \Psr\Log\InvalidArgumentException('Message cannot be empty');
        }

        $now = \DateTime::createFromFormat('U.u', microtime(true));
        $content = '[' . $now->format('Y-m-d H:i:s.u') . "]\n"
        . $message . "\n"
        . str_repeat('=', 20) . "\n\n";

        switch ($this->storage_type) {
            case self::STORAGE_TYPE_FILE:
                $this->appendToFile($this->log_path . $level . '.log', $content, $context);
                break;
            case self::STORAGE_TYPE_DB:
                $this->saveToDb($level, $content, $context);
                break;
        }
    }
}
