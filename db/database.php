<?php

class Database {
    private static $instance = null; // Singleton instance
    private $connection; // Database connection

    private $host;
    private $username;
    private $password;
    private $database;

    // Private constructor to prevent external instantiation
    private function __construct() {
        // Load environment variables
        $this->loadEnvVariables();

        try {
            $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database);

            // Check for connection errors
            if ($this->connection->connect_error) {
                error_log("Database connection failed: " . $this->connection->connect_error);
                throw new Exception("Database connection failed");
            }
        } catch (Exception $e) {
            error_log("Database Error: " . $e->getMessage());
            die("Unable to connect to the database. Please try again later.");
        }
    }

    // Load environment variables from .env file
    private function loadEnvVariables() {
        $envFile = __DIR__ . '/../.env';
        try {
            if (!file_exists($envFile)) {
                error_log("Environment file not found: " . $envFile);
                throw new Exception("Configuration error");
            }

            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    
                    switch ($key) {
                        case 'DB_HOST':
                            $this->host = $value;
                            break;
                        case 'DB_USERNAME':
                            $this->username = $value;
                            break;
                        case 'DB_PASSWORD':
                            $this->password = $value;
                            break;
                        case 'DB_DATABASE':
                            $this->database = $value;
                            break;
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Configuration Error: " . $e->getMessage());
            die("System configuration error. Please contact administrator.");
        }
    }

    // Public static method to get the singleton instance
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Method to get the database connection
    public function getConnection() {
        return $this->connection;
    }

    // Prevent cloning of the instance
    private function __clone() {}

    // Prevent unserializing of the instance
    public function __wakeup() {}
}

?>
