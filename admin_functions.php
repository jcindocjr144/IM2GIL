<?php 
require_once 'database.php';

class Admin extends Database {

    // Add a new subject
    public function addSubject($subject_name, $room_assignment, $start_date, $end_date, $schedule) {
        if ($this->getState()) {
            $stmt = $this->getDb()->prepare("INSERT INTO subjects (subject_name, room_assignment, start_date, end_date, schedule) VALUES (:subject_name, :room_assignment, :start_date, :end_date, :schedule)");
            $stmt->bindParam(':subject_name', $subject_name);
            $stmt->bindParam(':room_assignment', $room_assignment);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
            $stmt->bindParam(':schedule', $schedule);
            $stmt->execute();
            return "Subject added successfully with ID: " . $this->getDb()->lastInsertId();
        } else {
            return $this->getErrMsg();
        }
    }

    // Update a subject
    public function updateSubject($subject_id, $subject_name, $room_assignment, $start_date, $end_date, $schedule) {
        if ($this->getState()) {
            $stmt = $this->getDb()->prepare("UPDATE subjects SET subject_name = :subject_name, room_assignment = :room_assignment, start_date = :start_date, end_date = :end_date, schedule = :schedule WHERE id = :id");
            $stmt->bindParam(':subject_name', $subject_name);
            $stmt->bindParam(':room_assignment', $room_assignment);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
            $stmt->bindParam(':schedule', $schedule); // Added schedule binding
            $stmt->bindParam(':id', $subject_id);
            $stmt->execute();
            return "Subject updated successfully!";
        } else {
            return $this->getErrMsg();
        }
    }

    // Delete a subject
    public function deleteSubject($subject_id) {
        $stmt = $this->getDb()->prepare("DELETE FROM subjects WHERE id = :id");
        $stmt->bindParam(':id', $subject_id);
        $stmt->execute();
        return "Subject deleted successfully!";
    }

    // Get all subjects
    public function getSubjects() {
        $stmt = $this->getDb()->query("SELECT * FROM subjects");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add or update grade for a student in a subject
    public function addOrUpdateGrade($student_id, $subject_id, $grade) {
        // Check if subject_id exists
        $stmt = $this->getDb()->prepare("SELECT id FROM subjects WHERE id = :subject_id");
        $stmt->execute(['subject_id' => $subject_id]);
        
        if ($stmt->rowCount() == 0) {
            // Fetch available subject IDs for clearer error messages
            $availableSubjects = implode(', ', array_column($this->getSubjects(), 'id'));
            throw new Exception("Invalid subject_id: $subject_id. Available subject IDs: $availableSubjects");
        }

        // Proceed with the add or update operation
        $stmt = $this->getDb()->prepare("REPLACE INTO grades (student_id, subject_id, grade) VALUES (:student_id, :subject_id, :grade)");
        $stmt->bindParam(':student_id', $student_id);
        $stmt->bindParam(':subject_id', $subject_id);
        $stmt->bindParam(':grade', $grade);
        $stmt->execute();
        return "Grade added/updated successfully!";
    } 

    public function getAllStudents() {
        $stmt = $this->getDb()->prepare("SELECT * FROM users WHERE role = 'student'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function subjectExists($subject_name) {
        try {
            // Prepare the SQL statement with the correct placeholder
            $stmt = $this->getDb()->prepare("SELECT COUNT(*) FROM subjects WHERE subject_name = :subject_name");
            $stmt->bindParam(':subject_name', $subject_name);
            $stmt->execute();
            return $stmt->fetchColumn() > 0; // Returns true if a record is found
        } catch (PDOException $e) {
            // Log the error and return a friendly message
            error_log("Database error in subjectExists: " . $e->getMessage());
            return "Error: Could not check if the subject exists. Please try again later.";
        }
    }
    
    // Method to delete a student
    public function deleteStudent($student_id) {
        $stmt = $this->getDb()->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt->execute([$student_id])) {
            return "Student deleted successfully.";
        } else {
            throw new Exception("Failed to delete student.");
        }
    }

    // Delete grade
    public function deleteGrade($grade_id) {
        $stmt = $this->getDb()->prepare("DELETE FROM grades WHERE id = :id");
        $stmt->bindParam(':id', $grade_id);
        $stmt->execute();
        return "Grade deleted successfully!";
    }

    // View grades for all students
    public function getAllGrades() {
        $stmt = $this->getDb()->query(" SELECT g.id AS grade_id, st.id AS student_id, st.username, s.subject_name, g.grade
    FROM grades g
    JOIN students st ON g.student_id = st.id
    JOIN subjects s ON g.subject_id = s.id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Method to get the database connection
    public function getConnection() {
        return $this->getDb(); // Assuming getDb() is the method from Database that returns the PDO instance
    }
}
?>
