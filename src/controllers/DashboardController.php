<?php

class DashboardController {
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
        include __DIR__ . '/../views/dashboard.php';
    }
    
    public function getLinks() {
        $link = new Link($this->db);
        $stmt = $link->getAll();
        $links = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $links[] = [
                'id' => $id,
                'name' => $name,
                'url' => $url,
                'icon' => $icon_url,
                'sort_order' => $sort_order
            ];
        }
        return $links;
    }
    
    public function createLink($data) {
        $link = new Link($this->db);
        $link->name = $data['name'];
        $link->url = $data['url'];
        $link->icon_url = $data['icon_url'];
        $link->sort_order = $data['sort_order'];
        
        return $link->create();
    }
    
    public function updateLink($data) {
        $link = new Link($this->db);
        $link->id = $data['id'];
        $link->name = $data['name'];
        $link->url = $data['url'];
        $link->icon_url = $data['icon_url'];
        $link->sort_order = $data['sort_order'];
        
        return $link->update();
    }
    
    public function deleteLink($id) {
        $link = new Link($this->db);
        $link->id = $id;
        
        return $link->delete();
    }
}