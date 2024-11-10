<?php
require_once 'database.php';

class Student extends Database {
    // Method to fetch grades for a student
    public function getGrades($student_id) {
        if ($this->getState()) { // Check connection state
            $stmt = $this->getDb()->prepare("SELECT s.subject_name, g.grade 
                                             FROM grades g
                                             JOIN subjects s ON g.subject_id = s.id
                                             WHERE g.student_id = :student_id");
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return [];
        }
    }
}
?>
