<?php

class Link {
    private $conn;
    private $table_name = "homepage_links";

    public $id;
    public $name;
    public $url;
    public $icon_url;
    public $sort_order;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all links
    public function getAll() {
        $query = "SELECT id, name, url, icon_url, sort_order FROM " . $this->table_name . " ORDER BY sort_order";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Get a link by ID
    public function getById($id) {
        $query = "SELECT id, name, url, icon_url, sort_order FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt;
    }

    // Create a link
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET name=:name, url=:url, icon_url=:icon_url, sort_order=:sort_order";
        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->url = htmlspecialchars(strip_tags($this->url));
        $this->icon_url = htmlspecialchars(strip_tags($this->icon_url));
        $this->sort_order = htmlspecialchars(strip_tags($this->sort_order));

        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":url", $this->url);
        $stmt->bindParam(":icon_url", $this->icon_url);
        $stmt->bindParam(":sort_order", $this->sort_order);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Update a link
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET name=:name, url=:url, icon_url=:icon_url, sort_order=:sort_order WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->url = htmlspecialchars(strip_tags($this->url));
        $this->icon_url = htmlspecialchars(strip_tags($this->icon_url));
        $this->sort_order = htmlspecialchars(strip_tags($this->sort_order));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":url", $this->url);
        $stmt->bindParam(":icon_url", $this->icon_url);
        $stmt->bindParam(":sort_order", $this->sort_order);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete a link
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind value
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Get the next sort order value
    public function getNextSortOrder() {
        $query = "SELECT COALESCE(MAX(sort_order), 0) + 1 AS next_order FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['next_order'];
    }
}