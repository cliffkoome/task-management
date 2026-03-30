<?php

class Task {
    private $conn;
    private $table = 'tasks';

    public $id;
    public $title;
    public $due_date;
    public $priority;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all tasks, sorted by priority then due_date
    public function getAll($status = null) {
        $priorityOrder = "FIELD(priority, 'high', 'medium', 'low')";

        $query = "SELECT * FROM {$this->table}";

        if ($status) {
            $query .= " WHERE status = :status";
        }

        $query .= " ORDER BY {$priorityOrder}, due_date ASC";

        $stmt = $this->conn->prepare($query);

        if ($status) {
            $stmt->bindParam(':status', $status);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Create a new task
    public function create() {
        $query = "INSERT INTO {$this->table} 
                  (title, due_date, priority, status) 
                  VALUES (:title, :due_date, :priority, 'pending')";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':title',    $this->title);
        $stmt->bindParam(':due_date', $this->due_date);
        $stmt->bindParam(':priority', $this->priority);

        return $stmt->execute();
    }

    // Check for duplicate title on same due_date
    public function isDuplicate() {
        $query = "SELECT id FROM {$this->table} 
                  WHERE title = :title AND due_date = :due_date";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':title',    $this->title);
        $stmt->bindParam(':due_date', $this->due_date);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Find task by ID
    public function findById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch();
    }

    // Update task status
    public function updateStatus($id, $newStatus) {
        $query = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':status', $newStatus);
        $stmt->bindParam(':id',     $id);

        return $stmt->execute();
    }

    // Delete a task
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    // Daily report
    public function getDailyReport($date) {
        $query = "SELECT priority, status, COUNT(*) as count 
                  FROM {$this->table} 
                  WHERE due_date = :date 
                  GROUP BY priority, status";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Get last inserted ID
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
}