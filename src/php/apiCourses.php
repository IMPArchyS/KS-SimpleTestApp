<?php
require_once "../config.php";
require_once "Course.php";

$course = new Course($conn);

$method = $_SERVER['REQUEST_METHOD'];

$requestUri = explode('/', $_SERVER['REQUEST_URI']);
$endpoint = end($requestUri);
$parsed_url = parse_url($endpoint);
$endpoint = $parsed_url['path'];


if ($endpoint === 'courses') {
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                // Get course by ID
                $courseId = $_GET['id'];
                $result = $course->getCourseById($courseId);
                if ($result) {
                    echo json_encode($result);
                } else {
                    http_response_code(404);
                    echo json_encode(array("message" => "Course not found."));
                }
            } else {
                $result = $course->getAllCourses();
                echo json_encode($result);
            }
            break;
        case 'POST':
            $data = json_decode(file_get_contents("php://input"), true);

            if ($course->addCourse($data)) {
                http_response_code(201);
                echo json_encode(array("message" => "Course created successfully."));
            } else {
                http_response_code(400);
                echo json_encode(array("message" => "Unable to create course."));
            }
            break;
        case 'PUT':
            if (isset($_GET['id'])) {
                $data = json_decode(file_get_contents("php://input"), true);
                $courseId = $_GET['id'];

                if (! $course->getCourseById($courseId)) {
                    http_response_code(404);
                    echo json_encode(array("message" => "Course not found."));
                    break;
                }
                if ($course->updateCourse($courseId, $data)) {
                    echo json_encode(array("message" => "Course updated successfully."));
                } else {
                    http_response_code(500);
                    echo json_encode(array("message" => "Unable to update course."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("message" => "Missing course ID."));
            }
            break;
        case 'DELETE':
            if (isset($_GET['id'])) {
                $courseId = $_GET['id'];

                if ($course->deleteCourse($courseId)) {
                    echo json_encode(array("message" => "Course deleted successfully."));
                } else {
                    http_response_code(500);
                    echo json_encode(array("message" => "Unable to delete course."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("message" => "Missing course ID."));
            }
            break;
        default:
            http_response_code(405); // Method Not Allowed
            echo json_encode(array("message" => "Method not allowed."));
            break;
    }
} else {
    // Invalid endpoint
    http_response_code(404);
    echo json_encode(array("message" => "Requested URI: ".$endpoint));
    echo json_encode(array("message" => "Endpoint not found."));
}