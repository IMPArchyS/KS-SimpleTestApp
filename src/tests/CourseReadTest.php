<?php
require_once __DIR__.'/../php/Course.php';

use PHPUnit\Framework\TestCase;

class CourseReadTest extends TestCase
{
    private $mockPDO;
    private $mockStmt;
    private $course;

    protected function setUp(): void
    {
        // Create mock objects
        $this->mockPDO = $this->createMock(PDO::class);
        $this->mockStmt = $this->createMock(PDOStatement::class);

        // Set up the course instance with mock
        $this->course = new Course($this->mockPDO);
    }

    public function testGetAllCourses()
    {
        // Set up mock expectations
        $expectedResults = [
            ['id' => 1, 'den' => 'Po', 'cas_od' => '08:00:00', 'cas_do' => '09:50:00', 'typ_akcie' => 'Prednáška', 'nazov_akcie' => 'Test Course', 'miestnost' => 'A123', 'vyucujuci' => 'John Doe'],
            ['id' => 2, 'den' => 'Ut', 'cas_od' => '10:00:00', 'cas_do' => '11:50:00', 'typ_akcie' => 'Cvičenie', 'nazov_akcie' => 'Test Course 2', 'miestnost' => 'B456', 'vyucujuci' => 'Jane Smith']
        ];

        // Configure mocks
        $this->mockPDO->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM rozvrh')
            ->willReturn($this->mockStmt);

        $this->mockStmt->expects($this->once())
            ->method('execute');

        $this->mockStmt->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($expectedResults);

        // Call the method being tested
        $result = $this->course->getAllCourses();

        // Assert the expected results
        $this->assertIsArray($result);
        $this->assertCount(1, $result); // Array containing one element (fetchAll results)
        $this->assertSame($expectedResults, $result[0]);
    }

    public function testGetCourseById()
    {
        // Set up test data
        $courseId = 1;
        $expectedCourse = ['id' => 1, 'den' => 'Po', 'cas_od' => '08:00:00', 'cas_do' => '09:50:00', 'typ_akcie' => 'Prednáška', 'nazov_akcie' => 'Test Course', 'miestnost' => 'A123', 'vyucujuci' => 'John Doe'];

        // Configure mocks
        $this->mockPDO->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM rozvrh WHERE id = :id')
            ->willReturn($this->mockStmt);

        // Need to return true from bindParam
        $this->mockStmt->expects($this->once())
            ->method('bindParam')
            ->with(':id', $courseId)
            ->willReturn(true);

        $this->mockStmt->expects($this->once())
            ->method('execute');

        $this->mockStmt->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($expectedCourse);

        // Call the method being tested
        $result = $this->course->getCourseById($courseId);

        // Assert the expected results
        $this->assertSame($expectedCourse, $result);
    }

    public function testGetCourseByIdNotFound()
    {
        $courseId = 999; // Non-existent ID

        // Configure mocks
        $this->mockPDO->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStmt);

        // Need to return true from bindParam
        $this->mockStmt->expects($this->once())
            ->method('bindParam')
            ->with(':id', $courseId)
            ->willReturn(true);

        $this->mockStmt->expects($this->once())
            ->method('execute');

        $this->mockStmt->expects($this->once())
            ->method('fetch')
            ->willReturn(false);

        // Call the method being tested
        $result = $this->course->getCourseById($courseId);

        // Assert the expected result
        $this->assertFalse($result);
    }
}