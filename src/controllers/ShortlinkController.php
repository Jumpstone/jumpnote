<?php

class ShortlinkController {
    private $db;
    
    public function __construct($database) {
        if ($database === null) {
            throw new Exception("Database connection is null");
        }
        $this->db = $database;
    }
    
    public function index() {
        // In a real implementation, you would fetch data from the database
        // For now, we'll just include the view
        include __DIR__ . '/../views/shortlinks.php';
    }
    
    public function getElements() {
        $element = new ShortlinkElement($this->db);
        $stmt = $element->getAll();
        $elements = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $elements[] = [
                'id' => $id,
                'name' => $name,
                'url' => $url,
                'icon_url' => $icon_url,
                'description' => $description,
                'element_type' => $element_type,
                'parent_id' => $parent_id,
                'section_id' => $section_id,
                'sort_order' => $sort_order
            ];
        }
        return $elements;
    }
    
    public function createElement($data) {
        $element = new ShortlinkElement($this->db);
        $element->name = $data['name'];
        $element->url = $data['url'];
        $element->icon_url = $data['icon_url'];
        $element->description = $data['description'];
        $element->element_type = $data['element_type'];
        $element->parent_id = $data['parent_id'];
        $element->section_id = $data['section_id'];
        $element->sort_order = $data['sort_order'];
        
        return $element->create();
    }
    
    public function updateElement($data) {
        $element = new ShortlinkElement($this->db);
        $element->id = $data['id'];
        $element->name = $data['name'];
        $element->url = $data['url'];
        $element->icon_url = $data['icon_url'];
        $element->description = $data['description'];
        $element->element_type = $data['element_type'];
        $element->parent_id = $data['parent_id'];
        $element->section_id = $data['section_id'];
        $element->sort_order = $data['sort_order'];
        
        return $element->update();
    }
    
    public function deleteElement($id) {
        $element = new ShortlinkElement($this->db);
        $element->id = $id;
        
        return $element->delete();
    }
}