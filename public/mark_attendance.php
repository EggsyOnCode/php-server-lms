<?php
session_start();

// Check if the user is a teacher
if ($_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

require_once '../src/config.php'; // Include database configuration
require_once '../src/attendance.php'; // Include attendance-related functions

// Get the class ID and date from the URL or use today's date
$classid = intval($_GET['classid'] ?? 0);
$date = $_GET['date'] ?? date('Y-m-d');

if ($classid <= 0) {
    die("Invalid class ID.");
}

// Fetch existing attendance for the given class and date
$attendanceData = getAttendanceForDate($classid, $date);

// Fetch class details
$classDetails = getClassDetails($classid);

?>

<?php include '../templates/master.php'; ?>
<div class="container large">
    <h2>Mark Attendance for Class ID: <?php echo $classid; ?> on <?php echo $date; ?></h2>
    <h3>Class Details</h3>
    <p>Start Time: <?php echo $classDetails['starttime']; ?></p>
    <p>End Time: <?php echo $classDetails['endtime']; ?></p>
    
    <form id="attendanceForm" method="POST">
        <input type="hidden" name="classid" value="<?php echo $classid; ?>">
        <input type="hidden" name="date" value="<?php echo $date; ?>">
        <div style="display: flex; flex-direction: column; gap: 10px;">
            <?php foreach ($attendanceData as $student): ?>
                <div class="student-row" style="display: flex; align-items: center; justify-content: space-around; border: 1px solid #ddd; padding: 10px; border-radius: 5px;">
                    <div><strong>Roll No:</strong> <?php echo $student['studentid']; ?></div>
                    <div><strong>Name:</strong> <?php echo $student['fullname']; ?></div>
                    <div>

                        <button 
                                type="button" 
                                class="attendance-btn" 
                                data-student-id="<?php echo $student['studentid']; ?>" 
                                data-status="<?php echo isset($student['isPresent']) && $student['isPresent'] == 1 ? '1' : '0'; ?>"
                                style="background-color: <?php echo isset($student['isPresent']) && $student['isPresent'] == 1 ? 'green' : 'red'; ?>; color: white; border: none; padding: 10px 20px; border-radius: 5px;">
                                <?php echo isset($student['isPresent']) && $student['isPresent'] == 1 ? 'Present' : 'Absent'; ?>
                            </button>
                    </div>
                    
    <input type="hidden" name="attendance[<?php echo $student['studentid']; ?>][isPresent]" value="<?php echo isset($student['isPresent']) ? $student['isPresent'] : '0'; ?>">
    <div>
                        <input type="text" name="attendance[<?php echo $student['studentid']; ?>][comments]" placeholder="Comments" 
                            value="<?php echo $student['comments'] ?? ''; ?>">
                    </div>
                          
</div>
<?php endforeach; ?> 
                   
                </div>
                
          
            <button type="submit" style="margin-top: 20px; padding: 10px 20px; background-color: blue; color: white; border: none; border-radius: 5px;">
            Submit Attendance
        </button> 
        </div>
        
    </form>
</div>

<script>
    document.getElementById('attendanceForm').addEventListener('submit', function(event) {
        event.preventDefault();
        
        // Prepare the data to send
        const formData = new FormData(this);
        const updates = {};
        formData.forEach((value, key) => {
            const match = key.match(/^attendance\[(\d+)\]\[(\w+)\]$/);
            if (match) {
                const studentId = match[1];
                const field = match[2];
                if (!updates[studentId]) updates[studentId] = {};
                updates[studentId][field] = value;
            }
        });

        // Send the updates to the server
        fetch('update_attendance.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                classid: <?php echo $classid; ?>,
                date: "<?php echo $date; ?>",
                updates: updates
            })
        }).then(response => response.json()).then(data => {
            if (data.success) {
                alert('Attendance updated successfully!');
            } else {
                alert('Failed to update attendance.');
            }
        });
    });
    document.querySelectorAll('.attendance-btn').forEach(button => {
        button.addEventListener('click', function () {
            // Get current state
            const currentState = this.dataset.status;

            // Toggle state
            const newState = currentState === '1' ? '0' : '1';
            this.dataset.status = newState;

            // Update button appearance and text
            if (newState === '1') {
                this.style.backgroundColor = 'green';
                this.textContent = 'Present';
            } else {
                this.style.backgroundColor = 'red';
                this.textContent = 'Absent';
            }

            // Update hidden input value
            const studentId = this.dataset.studentId;
            const hiddenInput = document.querySelector(`input[name="attendance[${studentId}][isPresent]"]`);
            if (hiddenInput) {
                hiddenInput.value = newState;
            }
        });
    });
</script>
