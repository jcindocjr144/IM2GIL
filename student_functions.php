<?php
require_once 'database.php';

class Student extends Database {
    // Method to fetch grades for a student
    public function getGrades($student_id) {
        if ($this->getState()) { // Check if the connection is active
            // Prepare the SQL query to fetch grades along with student information
            $stmt = $this->getDb()->prepare("
                SELECT st.username, s.subject_name, g.grade
                FROM grades g
                JOIN subjects s ON g.subject_id = s.id
                JOIN students st ON g.student_id = st.id
                WHERE g.student_id = :student_id
            ");
    
            // Bind the student ID to the query
            $stmt->bindParam(':student_id', $student_id);
    
            // Execute the query
            $stmt->execute();
    
            // Return the fetched results
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // If the connection is not active, return an empty array
            return [];
        }
    }
    
    public function getState() {
        return isset($this->db) && $this->db instanceof PDO;
    }
    
}    
?>
