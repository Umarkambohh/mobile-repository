<?php
/**
 * Database Connection File
 * Mobile Repository System
 * Handles MySQL database connection using PDO with exception handling
 */

class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "mobile_repo";
    private $conn;

    /**
     * Create database connection
     * @return PDO connection object
     */
    public function getConnection() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname}",
                $this->username,
                $this->password
            );
            
            // Set PDO error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Set default fetch mode to associative array
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            return $this->conn;
            
        } catch(PDOException $e) {
            // Log error and display user-friendly message
            error_log("Database Connection Error: " . $e->getMessage());
            die("Database connection failed. Please check your database configuration.");
        }
    }

    /**
     * Close database connection
     */
    public function closeConnection() {
        $this->conn = null;
    }
}

// Create database instance for use in other files
$database = new Database();
?>
