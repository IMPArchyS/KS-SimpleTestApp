<?php
require_once "../config.php";
require_once "Course.php";

$course = new Course($conn);

$method = $_SERVER['REQUEST_METHOD'];

$requestUri = explode('/', $_SERVER['REQUEST_URI']);
$endpoint = end($requestUri);
$parsed_url = parse_url($endpoint);
$endpoint = $parsed_url['path'];


$requestUri = explode('/', $_SERVER['REQUEST_URI']);
$endpointParts = array_slice($requestUri, -2); // Get the last two parts

// If the URL pattern is "courses/{id}"
if ($endpointParts[0] === 'courses' && isset($endpointParts[1]) && is_numeric($endpointParts[1])) {
    $courseId = $endpointParts[1];
    switch ($method) {
        case 'GET':
            $result = $course->getCourseById($courseId);
            if ($result) {
                echo json_encode($result);
            } else {
                sendNotFound("Course not found.");
            }
            break;
        case 'PUT':
            handlePutRequest($course, $courseId);
            break;
        case 'DELETE':
            if ($course->deleteCourse($courseId)) {
                echo json_encode(array("message" => "Course deleted successfully."));
            } else {
                sendBadRequest("Unable to delete course.");
            }
            break;
        default:
            sendMethodNotAllowed();
            break;
    }
}
// If the URL pattern is just "courses"
elseif (end($requestUri) === 'courses') {
    switch ($method) {
        case 'GET':
            $result = $course->getAllCourses();
            echo json_encode($result);
            break;
        case 'POST':
            handlePostRequest($course);
            break;
        default:
            sendMethodNotAllowed();
            break;
    }
} else {
    sendInvalidEndpoint(implode('/', $requestUri));
}
function handleGetRequest($course)
{
    if (isset($_GET['id'])) {
        $courseId = $_GET['id'];
        $result = $course->getCourseById($courseId);
        if ($result) {
            echo json_encode($result);
        } else {
            sendNotFound("Course not found.");
        }
    } else {
        $result = $course->getAllCourses();
        echo json_encode($result);
    }
}

function handlePostRequest($course)
{
    $data = json_decode(file_get_contents("php://input"), true);

    if ($course->addCourse($data)) {
        http_response_code(201);
        echo json_encode(array("message" => "Course created successfully."));
    } else {
        sendBadRequest("Unable to create course.");
    }
}

function handlePutRequest($course, $courseId = null)
{
    // If courseId is provided directly to the function
    if ($courseId !== null) {
        $data = json_decode(file_get_contents("php://input"), true);

        if (! $course->getCourseById($courseId)) {
            sendNotFound("Course not found.");
            return;
        }

        if ($course->updateCourse($courseId, $data)) {
            echo json_encode(array("message" => "Course updated successfully."));
        } else {
            sendBadRequest("Unable to update course.");
        }
    }
    // For backward compatibility, still check GET parameters
    elseif (isset($_GET['id'])) {
        $data = json_decode(file_get_contents("php://input"), true);
        $courseId = $_GET['id'];

        if (! $course->getCourseById($courseId)) {
            sendNotFound("Course not found.");
            return;
        }

        if ($course->updateCourse($courseId, $data)) {
            echo json_encode(array("message" => "Course updated successfully."));
        } else {
            sendBadRequest("Unable to update course.");
        }
    } else {
        sendNotFound("Missing course ID.");
    }
}
function handleDeleteRequest($course)
{
    if (isset($_GET['id'])) {
        $courseId = $_GET['id'];

        if ($course->deleteCourse($courseId)) {
            echo json_encode(array("message" => "Course deleted successfully."));
        } else {
            sendBadRequest("Unable to delete course.");
        }
    } else {
        sendNotFound("Missing course ID.");
    }
}

function sendNotFound($message)
{
    http_response_code(404);
    echo json_encode(array("message" => $message));
}

function sendBadRequest($message)
{
    http_response_code(400);
    echo json_encode(array("message" => $message));
}

function sendMethodNotAllowed()
{
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed."));
}

function sendInvalidEndpoint($endpoint)
{
    http_response_code(404);
    echo json_encode(array("message" => "Requested URI: ".$endpoint));
    echo json_encode(array("message" => "Endpoint not found."));
}