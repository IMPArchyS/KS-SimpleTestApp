<?php
// Check if our functions are already defined
if (! function_exists('custom_http_response_code')) {
    function custom_http_response_code($code = NULL)
    {
        if (isset($GLOBALS['mock_response_code'])) {
            return $GLOBALS['mock_response_code']($code);
        }
        return http_response_code($code);
    }
}

if (! function_exists('get_request_body')) {
    function get_request_body()
    {
        if (isset($GLOBALS['mock_file_get_contents'])) {
            return $GLOBALS['mock_file_get_contents'];
        }
        return file_get_contents("php://input");
    }
}

// The rest of the API logic will execute every time
if (isset($GLOBALS['course'])) {
    $course = $GLOBALS['course'];
} else {
    require_once "../config.php";
    require_once "Course.php";
    $course = new Course($conn);
}

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = explode('/', $_SERVER['REQUEST_URI']);

// Extract relevant parts from URL
$endpointParts = array_slice($requestUri, -2);

// Route handling
if ($endpointParts[0] === 'courses' && isset($endpointParts[1]) && is_numeric($endpointParts[1])) {
    // Handle /courses/{id} endpoints
    $courseId = $endpointParts[1];

    switch ($method) {
        case 'GET':
            $result = $course->getCourseById($courseId);
            if ($result) {
                echo json_encode($result);
            } else {
                custom_http_response_code(404);
                echo json_encode(array("message" => "Course not found."));
            }
            break;

        case 'PUT':
            $data = json_decode(get_request_body(), true);

            if (! $course->getCourseById($courseId)) {
                custom_http_response_code(404);
                echo json_encode(array("message" => "Course not found."));
                break;
            }

            if ($course->updateCourse($courseId, $data)) {
                echo json_encode(array("message" => "Course updated successfully."));
            } else {
                custom_http_response_code(400);
                echo json_encode(array("message" => "Unable to update course."));
            }
            break;

        case 'DELETE':
            if ($course->deleteCourse($courseId)) {
                echo json_encode(array("message" => "Course deleted successfully."));
            } else {
                custom_http_response_code(400);
                echo json_encode(array("message" => "Unable to delete course."));
            }
            break;

        default:
            custom_http_response_code(405);
            echo json_encode(array("message" => "Method not allowed."));
            break;
    }
} elseif (end($requestUri) === 'courses') {
    // Handle /courses endpoints
    switch ($method) {
        case 'GET':
            $result = $course->getAllCourses();
            echo json_encode($result);
            break;

        case 'POST':
            $data = json_decode(get_request_body(), true);

            if ($course->addCourse($data)) {
                custom_http_response_code(201);
                echo json_encode(array("message" => "Course created successfully."));
            } else {
                custom_http_response_code(400);
                echo json_encode(array("message" => "Unable to create course."));
            }
            break;

        default:
            custom_http_response_code(405);
            echo json_encode(array("message" => "Method not allowed."));
            break;
    }
} else {
    // Invalid endpoint
    custom_http_response_code(404);
    echo json_encode(array("message" => "Requested URI: ".implode('/', $requestUri)));
}