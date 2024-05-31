<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'dbconn.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM department";
    $result = $db->query($sql);

    $departments = [];

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $department_id = $row['id'];
        $sql_courses = "SELECT * FROM course WHERE department_id = $department_id";
        $result_courses = $db->query($sql_courses);

        $courses = [];
        while ($course_row = $result_courses->fetch(PDO::FETCH_ASSOC)) {
            $course_id = $course_row['id'];
            $sql_subjects = "SELECT * FROM subject WHERE course_id = $course_id";
            $result_subjects = $db->query($sql_subjects);

            $subjects = [];
            while ($subject_row = $result_subjects->fetch(PDO::FETCH_ASSOC)) {
                $subjects[] = [
                    'name' => $subject_row['name'],
                    'year' => $subject_row['year']
                ];
            }

            $courses[] = [
                'name' => $course_row['name'],
                'subjects' => $subjects
            ];
        }

        $departments[] = [
            'name' => $row['name'],
            'course' => $courses
        ];
    }

    echo json_encode($departments);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['name'])) {
        $name = $data['name'];

        $sql = "INSERT INTO department (name) VALUES (:name)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':name', $name);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(['message' => 'Department created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to create department']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid input']);
    }
}elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['id']) && isset($data['name'])) {
        $id = $data['id'];
        $name = $data['name'];

        $sql = "UPDATE department SET name = :name WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);

        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(['message' => 'Department updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to update department']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid input']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['name'])) {
        $name = $data['name'];

        $sql = "DELETE FROM department WHERE name = :name";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':name', $name);

        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(['message' => 'Department deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to delete department']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid input']);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
?>
