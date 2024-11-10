$(document).ready(function () {
    $('#updateSubjectModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var subjectId = button.data('id'); // Extract info from data-* attributes
        var subjectName = button.data('name');

        // Update the modal's content
        var modal = $(this);
        modal.find('#modal_subject_id').val(subjectId);
        modal.find('#modal_subject_name').val(subjectName);
    });
});

function openNav() {
document.getElementById("mySidenav").style.width = "250px";
document.getElementById("main").style.marginLeft = "250px";
}

/* Set the width of the side navigation to 0 and the left margin of the page content to 0 */
function closeNav() {
document.getElementById("mySidenav").style.width = "0";
document.getElementById("main").style.marginLeft = "0";
}
function students(){
    document.getElementById("students").style.display = "block";
    document.getElementById("subjects").style.display = "none";
    document.getElementById("grades").style.display = "none";
}
function subjects(){
    document.getElementById("subjects").style.display = "block";
    document.getElementById("grades").style.display = "none";
    document.getElementById("students").style.display = "none";
}
function grades(){
    document.getElementById("grades").style.display = "block";
    document.getElementById("subjects").style.display = "none";
    document.getElementById("students").style.display = "none";
}
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

    // Script to populate the modal with existing subject data
    const updateSubjectModal = document.getElementById('updateSubjectModal');
    updateSubjectModal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget; // Button that triggered the modal
        const subjectId = button.getAttribute('data-id');
        const subjectName = button.getAttribute('data-name');
        const roomAssignment = button.getAttribute('data-room');
        const startDate = button.getAttribute('data-start');
        const endDate = button.getAttribute('data-end');
        const schedule = button.getAttribute('data-schedule');

        // Update the modal's content
        const modalSubjectId = updateSubjectModal.querySelector('#modal_subject_id');
        const modalSubjectName = updateSubjectModal.querySelector('#modal_subject_name');
        const modalRoomAssignment = updateSubjectModal.querySelector('#modal_room_assignment');
        const modalStartDate = updateSubjectModal.querySelector('#modal_start_date');
        const modalEndDate = updateSubjectModal.querySelector('#modal_end_date');
        const modalSchedule = updateSubjectModal.querySelector('#modal_schedule');

        modalSubjectId.value = subjectId;
        modalSubjectName.value = subjectName;
        modalRoomAssignment.value = roomAssignment;
        modalStartDate.value = startDate;
        modalEndDate.value = endDate;
        modalSchedule.value = schedule;
    });