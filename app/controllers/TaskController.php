<?php

class TaskController {
    private $task;

    public function __construct($task) {
        $this->task = $task;
    }

    // POST /api/tasks
    public function createTask($data) {
        // Validate required fields
        if (empty($data['title']) || empty($data['due_date']) || empty($data['priority'])) {
            http_response_code(422);
            return ['error' => 'title, due_date and priority are required'];
        }

        // Validate priority
        if (!in_array($data['priority'], ['low', 'medium', 'high'])) {
            http_response_code(422);
            return ['error' => 'priority must be low, medium or high'];
        }

        // Validate due_date is today or later
        $today   = date('Y-m-d');
        $dueDate = $data['due_date'];

        if ($dueDate < $today) {
            http_response_code(422);
            return ['error' => 'due_date must be today or a future date'];
        }

        // Check for duplicate title on same due_date
        $this->task->title    = $data['title'];
        $this->task->due_date = $dueDate;
        $this->task->priority = $data['priority'];

        if ($this->task->isDuplicate()) {
            http_response_code(422);
            return ['error' => 'A task with the same title and due_date already exists'];
        }

        // Create the task
        if ($this->task->create()) {
            $id          = $this->task->lastInsertId();
            $createdTask = $this->task->findById($id);
            http_response_code(201);
            return $createdTask;
        }

        http_response_code(500);
        return ['error' => 'Failed to create task'];
    }

    // GET /api/tasks
    public function listTasks($status = null) {
        // Validate status if provided
        if ($status && !in_array($status, ['pending', 'in_progress', 'done'])) {
            http_response_code(422);
            return ['error' => 'status must be pending, in_progress or done'];
        }

        $tasks = $this->task->getAll($status);

        if (empty($tasks)) {
            return [
                'message' => 'No tasks found',
                'data'    => []
            ];
        }

        return ['data' => $tasks];
    }

    // PATCH /api/tasks/{id}/status
    public function updateStatus($id, $data) {
        if (empty($data['status'])) {
            http_response_code(422);
            return ['error' => 'status is required'];
        }

        $task = $this->task->findById($id);

        if (!$task) {
            http_response_code(404);
            return ['error' => 'Task not found'];
        }

        // Status progression rules
        $allowed = [
            'pending'     => 'in_progress',
            'in_progress' => 'done',
        ];

        $currentStatus = $task['status'];
        $newStatus     = $data['status'];

        // Check if already done
        if ($currentStatus === 'done') {
            http_response_code(422);
            return ['error' => 'Task is already done and cannot be updated'];
        }

        // Check progression is valid
        if (!isset($allowed[$currentStatus]) || $allowed[$currentStatus] !== $newStatus) {
            http_response_code(422);
            return [
                'error'    => 'Invalid status transition',
                'allowed'  => "'{$currentStatus}' can only move to '{$allowed[$currentStatus]}'"
            ];
        }

        if ($this->task->updateStatus($id, $newStatus)) {
            $updatedTask = $this->task->findById($id);
            return [
                'message' => 'Status updated successfully',
                'data'    => $updatedTask
            ];
        }

        http_response_code(500);
        return ['error' => 'Failed to update status'];
    }

    // DELETE /api/tasks/{id}
    public function deleteTask($id) {
        $task = $this->task->findById($id);

        if (!$task) {
            http_response_code(404);
            return ['error' => 'Task not found'];
        }

        // Only done tasks can be deleted
        if ($task['status'] !== 'done') {
            http_response_code(403);
            return ['error' => 'Only tasks with status done can be deleted'];
        }

        if ($this->task->delete($id)) {
            return ['message' => 'Task deleted successfully'];
        }

        http_response_code(500);
        return ['error' => 'Failed to delete task'];
    }

    // GET /api/tasks/report?date=YYYY-MM-DD
    public function dailyReport($date) {
        if (empty($date)) {
            http_response_code(422);
            return ['error' => 'date parameter is required (YYYY-MM-DD)'];
        }

        // Validate date format
        $d = DateTime::createFromFormat('Y-m-d', $date);
        if (!$d || $d->format('Y-m-d') !== $date) {
            http_response_code(422);
            return ['error' => 'Invalid date format. Use YYYY-MM-DD'];
        }

        $rows = $this->task->getDailyReport($date);

        // Build summary structure
        $summary = [
            'high'   => ['pending' => 0, 'in_progress' => 0, 'done' => 0],
            'medium' => ['pending' => 0, 'in_progress' => 0, 'done' => 0],
            'low'    => ['pending' => 0, 'in_progress' => 0, 'done' => 0],
        ];

        foreach ($rows as $row) {
            $summary[$row['priority']][$row['status']] = (int) $row['count'];
        }

        return [
            'date'    => $date,
            'summary' => $summary
        ];
    }
}