<?php 
require_once 'admin_dashboard.php';
require_once 'admin_functions.php';
require_once 'database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="shortcut icon" href="image/cpclogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="adminstyles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
<div id="main">
<div class="row p-3">
        <div class="col-lg-11 col-11">
            <h1 class="text-warning"> Welcome <span class="text-info"><?php echo htmlspecialchars($_SESSION['username']); ?></span></h1>
        </div>
        <div class="col-lg-1 col-1">
            <span onclick="openNav()"><i class="fa-solid fa-bars p-3 bg-warning text-dark rounded-2"></i></span>
        </div>
    </div>
<!-- Manage Students Section -->
<div class="container-fluid bg-warning p-3 mt-2" id="students" style="display:none;">
    <h3 class="text-center p-3">Students Management</h3>
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <table class="table table-bordered bg-dark text-white">
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Username</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
                
                <tr>
                    <td><?php echo htmlspecialchars($student['id']); ?></td>
                    <td><?php echo htmlspecialchars($student['username']); ?></td>
                    <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($student['middle_name']); ?></td>
                    <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                    <td>
                        <form action="admin_dashboard.php" method="POST" class="d-inline">
                            <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                            <button type="submit" name="delete_student" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="container-fluid bg-warning p-3 mt-2" id="subjects" style="display:none">
    <h3 class="text-center p-3">Subjects Management</h3>
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <!-- Form to Add a New Subject -->
    <form action="admin_dashboard.php" method="POST" class="mb-4">
        <div class="mb-3">
            <label for="subject_name" class="form-label">Subject Name</label>
            <input type="text" name="subject_name" id="subject_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="room_assignment" class="form-label">Room Assigned</label>
            <input type="text" name="room_assignment" id="room_assignment" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="start_date" class="form-label">Start Date</label>
            <input type="date" name="start_date" id="start_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" name="end_date" id="end_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="schedule" class="form-label">Schedule</label>
            <select name="schedule" id="schedule" class="form-control" required>
                <option value="">Select Schedule</option>
                <option value="MWF">MWF (Monday, Wednesday, Friday)</option>
                <option value="TTH">TTH (Tuesday, Thursday)</option>
                <option value="SAT">SAT (Saturday)</option>
            </select>
        </div>
        <button type="submit" name="add_subject" class="btn btn-primary">Add Subject</button>
    </form>

    <!-- Table of Subjects with Update and Delete Options -->
    <table class="table table-bordered bg-dark text-white">
        <thead>
            <tr>
                <th>Subject ID</th>
                <th>Subject Name</th>
                <th>Room</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Schedule</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($subjects as $subject): ?>
                <tr>
                    <td><?php echo htmlspecialchars($subject['id']); ?></td>
                    <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                    <td><?php echo htmlspecialchars($subject['room_assignment']); ?></td>
                    <td><?php echo htmlspecialchars($subject['start_date']); ?></td>
                    <td><?php echo htmlspecialchars($subject['end_date']); ?></td>
                    <td><?php echo htmlspecialchars($subject['schedule']); ?></td>
                    <td>
                        <button class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#updateSubjectModal" 
                                data-id="<?php echo $subject['id']; ?>" 
                                data-name="<?php echo htmlspecialchars($subject['subject_name']); ?>" 
                                data-room="<?php echo htmlspecialchars($subject['room_assignment']); ?>" 
                                data-start="<?php echo htmlspecialchars($subject['start_date']); ?>" 
                                data-end="<?php echo htmlspecialchars($subject['end_date']); ?>" 
                                data-schedule="<?php echo htmlspecialchars($subject['schedule']); ?>">Update</button>
                        <form action="admin_dashboard.php" method="POST" class="d-inline">
                            <input type="hidden" name="subject_id" value="<?php echo $subject['id']; ?>">
                            <button type="submit" name="delete_subject" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal for Updating Subject -->
<div class="modal fade" id="updateSubjectModal" tabindex="-1" aria-labelledby="updateSubjectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="admin_dashboard.php" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateSubjectModalLabel">Update Subject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="subject_id" id="modal_subject_id">
                    <div class="mb-3">
                        <label for="modal_subject_name" class="form-label">Subject Name</label>
                        <input type="text" name="subject_name" id="modal_subject_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="modal_room_assignment" class="form-label">Room Assigned</label>
                        <input type="text" name="room_assignment" id="modal_room_assignment" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="modal_start_date" class="form-label">Start Date</label>
                        <input type="date" name="start_date" id="modal_start_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="modal_end_date" class="form-label">End Date</label>
                        <input type="date" name="end_date" id="modal_end_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="modal_schedule" class="form-label">Schedule</label>
                        <select name="schedule" id="modal_schedule" class="form-control" required>
                            <option value="">Select Schedule</option>
                            <option value="MWF">MWF (Monday, Wednesday, Friday)</option>
                            <option value="TTH">TTH (Tuesday, Thursday)</option>
                            <option value="SAT">SAT (Saturday)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="update_subject" class="btn btn-primary">Update Subject</button>
                </div>
            </div>
        </form>
    </div>
</div>


    <!-- Manage Grades Section -->
<div class="container-fluid bg-warning p-3 mt-2" id="grades" style="display:none">
    <h3 class="text-center p-3">Grades Management</h3>
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-6 col-12">
            <table class="table table-bordered bg-dark text-white">
                <thead>
                    <tr>
                    <th>ID</th>
                    <th>Student</th>
                        <th>Subject</th>
                        <th>Grade</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($grades as $grade): ?>
            <?php foreach ($students as $student): ?>
                
                <tr>
                    <td><?php echo htmlspecialchars($student['id']); ?></td>
                            <td><?php echo htmlspecialchars($grade['username']); ?></td>
                            <td><?php echo htmlspecialchars($grade['subject_name']); ?></td>
                            <td><?php echo htmlspecialchars($grade['grade']); ?></td>
                            <td>
                                <form action="admin_dashboard.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="grade_id" value="<?php echo $grade['grade_id']; ?>">
                                    <button type="submit" name="delete_grade" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                                <button 
                                    type="button" 
                                    class="btn btn-info btn-sm"
                                    onclick="openUpdateModal(<?php echo $grade['grade_id']; ?>, '<?php echo htmlspecialchars($grade['username']); ?>', '<?php echo htmlspecialchars($grade['subject_name']); ?>', '<?php echo htmlspecialchars($grade['grade']); ?>')"
                                >
                                    Update
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Form to Add Grades -->
        <div class="col-lg-6 col-12 p-5 rounded-3 bg-dark text-white">
            <form action="admin_dashboard.php" method="POST" class="mb-4">
                <div class="mb-3">
                    <label for="student_id" class="form-label">Student ID</label>
                    <input type="number" name="student_id" id="student_id" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="subject_id" class="form-label">Subject ID</label>
                    <input type="number" name="subject_id" id="subject_id" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="grade" class="form-label">Grade</label>
                    <input type="number" name="grade" id="grade" class="form-control" required min="1" max="5" step="0.01" placeholder="Enter grade (1.0 - 5.0)">
                </div>
                <button type="submit" name="add_or_update_grade" class="btn btn-primary">Add Grade</button>
            </form>
        </div>
    </div>
</div>

<!-- Update Grade Modal -->
<div class="modal fade" id="updateGradeModal" tabindex="-1" aria-labelledby="updateGradeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="admin_dashboard.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateGradeModalLabel">Update Grade</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="grade_id" id="modal_grade_id">
                    <div class="mb-3">
                        <label for="modal_student_name" class="form-label">Student</label>
                        <input type="text" id="modal_student_name" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="modal_subject_name" class="form-label">Subject</label>
                        <input type="text" id="modal_subject_name" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="modal_grade" class="form-label">Grade</label>
                        <input type="number" name="grade" id="modal_grade" class="form-control" required min="1" max="5" step="0.01">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_grade" class="btn btn-primary">Update Grade</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openUpdateModal(gradeId, studentName, subjectName, currentGrade) {
        // Set the values in the modal
        document.getElementById('modal_grade_id').value = gradeId;
        document.getElementById('modal_student_name').value = studentName;
        document.getElementById('modal_subject_name').value = subjectName;
        document.getElementById('modal_grade').value = currentGrade;
        
        // Show the modal
        var updateGradeModal = new bootstrap.Modal(document.getElementById('updateGradeModal'));
        updateGradeModal.show();
    }
</script>

<!-- Update Subject Modal -->
<div class="modal fade" id="updateSubjectModal" tabindex="-1" aria-labelledby="updateSubjectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="admin_dashboard.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateSubjectModalLabel">Update Subject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="subject_id" id="modal_subject_id">
                    <div class="mb-3">
                        <label for="modal_subject_name" class="form-label">Subject Name</label>
                        <input type="text" name="subject_name" id="modal_subject_name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="update_subject" class="btn btn-primary">Update Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
<div class="container-fluid">
    <div id="mySidenav" class="sidenav">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
  <a href="#" onclick="students()">Manage Students</a>
  <a href="#" onclick="subjects()">Manage Subjects</a>
  <a href="#" onclick="grades()">Manage Grades</a>
  <a href="logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a>
</div>

</div>
<script src="adminscript.js">
</script>

</body>
</html>
