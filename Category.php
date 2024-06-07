<?php
class Category {
    private $conn;
    private $table_name = "categories";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function truncate() {
        $query = "Truncate " . $this->table_name;
        $stmt = $this->conn->prepare($query);

        if($stmt->execute()) {
            return true;
        }
    }

    private function parentPathExists($parent_path) {
        if($parent_path == '/'){
            return true;
        }

        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE CONCAT(parent_path, id, '/') = :parent_path";
        $stmt = $this->conn->prepare($query);

        // sanitize
        $parent_path = htmlspecialchars(strip_tags($parent_path));

        // bind value
        $stmt->bindParam(":parent_path", $parent_path);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }

    // Get parent path by parent_id
    private function getParentPath($parent_id) {
        $query = "SELECT CONCAT(parent_path, id, '/') AS full_path FROM " . $this->table_name . " WHERE id = :parent_id";
        $stmt = $this->conn->prepare($query);

        // sanitize
        $parent_id = htmlspecialchars(strip_tags($parent_id));

        // bind value
        $stmt->bindParam(":parent_id", $parent_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['full_path'] : null;
    }

    // Create category
    public function create($name,$parent_id = 0) {
        if ($parent_id) {
            $parent_path = $this->getParentPath($parent_id);
            if ($parent_path === null) {
                echo "Error: parent_id does not exist.\n";
                return false;
            }
        } else {
            $parent_id = 0;
            $parent_path = '/';
        }

        $query = "INSERT INTO " . $this->table_name . " (name, parent_path) VALUES (:name, :parent_path)";
        $stmt = $this->conn->prepare($query);

        // sanitize
        $name = htmlspecialchars(strip_tags($name));
        $parent_path = htmlspecialchars(strip_tags($parent_path));

        // bind values
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":parent_path", $parent_path);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Retrieve subtree
    public function readSubtree($parent_id) {
        $parent_path = $this->getParentPath($parent_id);
        if ($parent_path === null) {
            echo "Error: parent_id does not exist.\n";
            return false;
        }

        $query = "SELECT * FROM categories WHERE parent_path LIKE '".$parent_path."%'";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();
        return $stmt;
    }

    // Delete subtree
    public function deleteSubtree($parent_id) {
        $parent_path = $this->getParentPath($parent_id);
        if ($parent_path === null) {
            echo "Error: parent_id does not exist.\n";
            return false;
        }

        $query = "DELETE FROM " . $this->table_name . " WHERE parent_path LIKE '".$parent_path."%'";
        $stmt = $this->conn->prepare($query);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Update parent path (not tested)
    public function updatePath($current_parent_path,$new_parent_path) {
        $query = "UPDATE " . $this->table_name . " SET parent_path = REPLACE(parent_path, :current_path, :new_path) WHERE parent_path LIKE :current_path_like";
        $stmt = $this->conn->prepare($query);

        // sanitize
        $current_parent_path = htmlspecialchars(strip_tags($current_parent_path));
        $new_parent_path = htmlspecialchars(strip_tags($new_parent_path));

        // bind values
        $stmt->bindParam(":current_path", $current_parent_path);
        $stmt->bindParam(":new_path", $new_parent_path);
        $stmt->bindParam(":current_path_like", $current_parent_path . '%');

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
