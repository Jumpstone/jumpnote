<?php

class ShortlinkElement {
    private $conn;
    private $table_name = "shortlink_elements";

    public $id;
    public $name;
    public $url;
    public $icon_url;
    public $description;
    public $element_type;
    public $parent_id;
    public $section_id;
    public $sort_order;

    public function __construct($db) {
        if ($db === null) {
            throw new Exception("Database connection is null");
        }
        $this->conn = $db;
    }

    // Get all elements
    public function getAll() {
        if ($this->conn === null) {
            throw new Exception("Database connection is null");
        }
        
        $query = "SELECT id, name, url, icon_url, description, element_type, parent_id, section_id, sort_order 
                  FROM " . $this->table_name . " 
                  ORDER BY COALESCE(section_id, id), sort_order";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Create an element
    public function create() {
        if ($this->conn === null) {
            throw new Exception("Database connection is null");
        }
        
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name=:name, url=:url, icon_url=:icon_url, description=:description, 
                      element_type=:element_type, parent_id=:parent_id, section_id=:section_id, sort_order=:sort_order";
        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->url = htmlspecialchars(strip_tags($this->url));
        $this->icon_url = htmlspecialchars(strip_tags($this->icon_url));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->element_type = htmlspecialchars(strip_tags($this->element_type));
        $this->parent_id = htmlspecialchars(strip_tags($this->parent_id));
        $this->section_id = htmlspecialchars(strip_tags($this->section_id));
        $this->sort_order = htmlspecialchars(strip_tags($this->sort_order));

        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":url", $this->url);
        $stmt->bindParam(":icon_url", $this->icon_url);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":element_type", $this->element_type);
        $stmt->bindParam(":parent_id", $this->parent_id);
        $stmt->bindParam(":section_id", $this->section_id);
        $stmt->bindParam(":sort_order", $this->sort_order);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Update an element
    public function update() {
        if ($this->conn === null) {
            throw new Exception("Database connection is null");
        }
        
        $query = "UPDATE " . $this->table_name . " 
                  SET name=:name, url=:url, icon_url=:icon_url, description=:description, 
                      element_type=:element_type, parent_id=:parent_id, section_id=:section_id, sort_order=:sort_order 
                  WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->url = htmlspecialchars(strip_tags($this->url));
        $this->icon_url = htmlspecialchars(strip_tags($this->icon_url));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->element_type = htmlspecialchars(strip_tags($this->element_type));
        $this->parent_id = htmlspecialchars(strip_tags($this->parent_id));
        $this->section_id = htmlspecialchars(strip_tags($this->section_id));
        $this->sort_order = htmlspecialchars(strip_tags($this->sort_order));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":url", $this->url);
        $stmt->bindParam(":icon_url", $this->icon_url);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":element_type", $this->element_type);
        $stmt->bindParam(":parent_id", $this->parent_id);
        $stmt->bindParam(":section_id", $this->section_id);
        $stmt->bindParam(":sort_order", $this->sort_order);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete an element
    public function delete() {
        if ($this->conn === null) {
            throw new Exception("Database connection is null");
        }
        
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
}