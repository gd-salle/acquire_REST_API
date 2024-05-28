<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$servername = "localhost";
$username = "root";
$password = "your-password";
$dbname = "curriculumDB";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM department";
$result = $conn->query($sql);

$departments = [];

while ($row = $result->fetch_assoc()) {
    $department_id = $row['id'];
    $sql_courses = "SELECT * FROM course WHERE department_id = $department_id";
    $result_courses = $conn->query($sql_courses);

    $courses = [];
    while ($course_row = $result_courses->fetch_assoc()) {
        $course_id = $course_row['id'];
        $sql_subjects = "SELECT * FROM subject WHERE course_id = $course_id";
        $result_subjects = $conn->query($sql_subjects);

        $subjects = [];
        while ($subject_row = $result_subjects->fetch_assoc()) {
            $subjects[] = $subject_row['name'];
        }

        $courses[] = [
            'name' => $course_row['name'],
            'subject' => $subjects
        ];
    }

    $departments[] = [
        'name' => $row['name'],
        'course' => $courses
    ];
}

echo json_encode($departments);

$conn->close();
?>
