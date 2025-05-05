<?php
require_once __DIR__.'/../php/Course.php';

use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class ApiCoursesTest extends TestCase
{
    private $mockCourse;
    private $originalServer;
    private $originalGet;
    private $responseCode;

    protected function setUp(): void
    {
        // Save original globals
        $this->originalServer = $_SERVER;
        $this->originalGet = $_GET;
        $this->responseCode = 200;

        // Create a mock Course class
        $this->mockCourse = $this->createMock(Course::class);

        // Reset globals that might have been set by previous tests
        unset($GLOBALS['course']);
        unset($GLOBALS['mock_response_code']);
        unset($GLOBALS['mock_file_get_contents']);
    }

    protected function tearDown(): void
    {
        // Restore original globals
        $_SERVER = $this->originalServer;
        $_GET = $this->originalGet;

        // Reset globals we set
        unset($GLOBALS['course']);
        unset($GLOBALS['mock_response_code']);
        unset($GLOBALS['mock_file_get_contents']);
    }

    /**
     * Helper function to execute the API code in a clean environment
     */
    private function executeApi($method, $uri, $body = null)
    {
        // Set up request parameters
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $uri;

        // Set up globals for the API to use
        $GLOBALS['course'] = $this->mockCourse;

        // Setup response code mock
        $self = $this;
        $GLOBALS['mock_response_code'] = function ($code = null) use ($self) {
            if ($code !== null) {
                $self->responseCode = $code;
            }
            return $self->responseCode;
        };

        if ($body) {
            $GLOBALS['mock_file_get_contents'] = json_encode($body);
        }

        // Capture output
        ob_start();
        require __DIR__.'/../php/ApiCoursesTestable.php';
        $output = ob_get_clean(); // Get and clean the buffer in one step

        return [
            'status' => $this->responseCode,
            'json' => ! empty($output) ? json_decode($output, true) : null
        ];
    }

    /**
     * @preserveGlobalState enabled
     */
    public function testGetAllCourses()
    {
        // Mock data
        $mockData = [
            [
                ['id' => 1, 'den' => 'Po', 'nazov_akcie' => 'Test Course 1'],
                ['id' => 2, 'den' => 'Ut', 'nazov_akcie' => 'Test Course 2']
            ]
        ];

        // Set up expectations
        $this->mockCourse->expects($this->once())
            ->method('getAllCourses')
            ->willReturn($mockData);

        // Execute the API
        $response = $this->executeApi('GET', '/php/apiCourses.php/courses');

        // Assertions
        $this->assertEquals(200, $response['status']);
        $this->assertEquals($mockData, $response['json']);
    }

    /**
     * @preserveGlobalState enabled
     */
    public function testGetCourseByIdSuccess()
    {
        // Mock data
        $mockCourse = ['id' => 1, 'den' => 'Po', 'nazov_akcie' => 'Test Course 1'];

        // Set up expectations
        $this->mockCourse->expects($this->once())
            ->method('getCourseById')
            ->with('1')
            ->willReturn($mockCourse);

        // Execute the API
        $response = $this->executeApi('GET', '/php/apiCourses.php/courses/1');

        // Assertions
        $this->assertEquals(200, $response['status']);
        $this->assertEquals($mockCourse, $response['json']);
    }

    /**
     * @preserveGlobalState enabled
     */
    public function testGetCourseByIdNotFound()
    {
        // Set up expectations
        $this->mockCourse->expects($this->once())
            ->method('getCourseById')
            ->with('999')
            ->willReturn(false);

        // Execute the API
        $response = $this->executeApi('GET', '/php/apiCourses.php/courses/999');

        // Assertions
        $this->assertEquals(404, $response['status']);
        $this->assertArrayHasKey('message', $response['json']);
        $this->assertEquals('Course not found.', $response['json']['message']);
    }

    /**
     * @preserveGlobalState enabled
     */
    public function testPostCourseSuccess()
    {
        // Mock data
        $postData = [
            'den' => 'St',
            'cas_od' => '13:00:00',
            'cas_do' => '14:50:00',
            'typ_akcie' => 'Prednáška',
            'nazov_akcie' => 'New Course',
            'miestnost' => 'B501',
            'vyucujuci' => 'New Teacher'
        ];

        // Set up expectations
        $this->mockCourse->expects($this->once())
            ->method('addCourse')
            ->with($this->equalTo($postData))
            ->willReturn(true);

        // Execute the API
        $response = $this->executeApi('POST', '/php/apiCourses.php/courses', $postData);

        // Assertions
        $this->assertEquals(201, $response['status']);
        $this->assertEquals('Course created successfully.', $response['json']['message']);
    }

    /**
     * @preserveGlobalState enabled
     */
    public function testPostCourseFailure()
    {
        // Mock data
        $postData = [
            'den' => 'St',
            'cas_od' => '13:00:00',
            // Missing required fields
        ];

        // Set up expectations
        $this->mockCourse->expects($this->once())
            ->method('addCourse')
            ->with($this->equalTo($postData))
            ->willReturn(false);

        // Execute the API
        $response = $this->executeApi('POST', '/php/apiCourses.php/courses', $postData);

        // Assertions
        $this->assertEquals(400, $response['status']);
        $this->assertEquals('Unable to create course.', $response['json']['message']);
    }

    /**
     * @preserveGlobalState enabled
     */
    public function testPutCourseSuccess()
    {
        // Mock data
        $putData = [
            'den' => 'St',
            'cas_od' => '13:00:00',
            'cas_do' => '14:50:00',
            'typ_akcie' => 'Prednáška',
            'nazov_akcie' => 'Updated Course',
            'miestnost' => 'B501',
            'vyucujuci' => 'Updated Teacher'
        ];

        // Set up expectations for getCourseById (course exists)
        $this->mockCourse->expects($this->once())
            ->method('getCourseById')
            ->with('1')
            ->willReturn(['id' => 1]);

        // Set up expectations for updateCourse
        $this->mockCourse->expects($this->once())
            ->method('updateCourse')
            ->with('1', $this->equalTo($putData))
            ->willReturn(true);

        // Execute the API
        $response = $this->executeApi('PUT', '/php/apiCourses.php/courses/1', $putData);

        // Assertions
        $this->assertEquals(200, $response['status']);
        $this->assertEquals('Course updated successfully.', $response['json']['message']);
    }

    /**
     * @preserveGlobalState enabled
     */
    public function testPutCourseNotFound()
    {
        // Mock data
        $putData = [
            'den' => 'St',
            'nazov_akcie' => 'Updated Course',
        ];

        // Set up expectations for getCourseById (course doesn't exist)
        $this->mockCourse->expects($this->once())
            ->method('getCourseById')
            ->with('999')
            ->willReturn(false);

        // updateCourse should not be called
        $this->mockCourse->expects($this->never())
            ->method('updateCourse');

        // Execute the API
        $response = $this->executeApi('PUT', '/php/apiCourses.php/courses/999', $putData);

        // Assertions
        $this->assertEquals(404, $response['status']);
        $this->assertEquals('Course not found.', $response['json']['message']);
    }

    /**
     * @preserveGlobalState enabled
     */
    public function testPutCourseUpdateFailure()
    {
        // Mock data
        $putData = [
            'den' => 'St',
            'nazov_akcie' => 'Updated Course',
        ];

        // Set up expectations for getCourseById (course exists)
        $this->mockCourse->expects($this->once())
            ->method('getCourseById')
            ->with('1')
            ->willReturn(['id' => 1]);

        // Set up expectations for updateCourse (fails)
        $this->mockCourse->expects($this->once())
            ->method('updateCourse')
            ->with('1', $this->equalTo($putData))
            ->willReturn(false);

        // Execute the API
        $response = $this->executeApi('PUT', '/php/apiCourses.php/courses/1', $putData);

        // Assertions
        $this->assertEquals(400, $response['status']);
        $this->assertEquals('Unable to update course.', $response['json']['message']);
    }

    /**
     * @preserveGlobalState enabled
     */
    public function testDeleteCourseSuccess()
    {
        // Set up expectations
        $this->mockCourse->expects($this->once())
            ->method('deleteCourse')
            ->with('1')
            ->willReturn(true);

        // Execute the API
        $response = $this->executeApi('DELETE', '/php/apiCourses.php/courses/1');

        // Assertions
        $this->assertEquals(200, $response['status']);
        $this->assertEquals('Course deleted successfully.', $response['json']['message']);
    }

    /**
     * @preserveGlobalState enabled
     */
    public function testDeleteCourseFailure()
    {
        // Set up expectations
        $this->mockCourse->expects($this->once())
            ->method('deleteCourse')
            ->with('999')
            ->willReturn(false);

        // Execute the API
        $response = $this->executeApi('DELETE', '/php/apiCourses.php/courses/999');

        // Assertions
        $this->assertEquals(400, $response['status']);
        $this->assertEquals('Unable to delete course.', $response['json']['message']);
    }

    /**
     * @preserveGlobalState enabled
     */
    public function testInvalidEndpoint()
    {
        // Execute the API
        $response = $this->executeApi('GET', '/php/apiCourses.php/invalid');

        // Assertions
        $this->assertEquals(404, $response['status']);
        $this->assertArrayHasKey('message', $response['json']);
    }

    /**
     * @preserveGlobalState enabled
     */
    public function testMethodNotAllowed()
    {
        // Execute the API with invalid method
        $response = $this->executeApi('PATCH', '/php/apiCourses.php/courses/1');

        // Assertions
        $this->assertEquals(405, $response['status']);
        $this->assertEquals('Method not allowed.', $response['json']['message']);
    }

    /**
     * @preserveGlobalState enabled
     */
    public function testMethodNotAllowedOnCollection()
    {
        // Execute the API with invalid method on the collection endpoint
        $response = $this->executeApi('PATCH', '/php/apiCourses.php/courses');

        // Assertions
        $this->assertEquals(405, $response['status']);
        $this->assertEquals('Method not allowed.', $response['json']['message']);
    }
}