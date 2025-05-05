<?php
require_once __DIR__.'/../php/Course.php';

use PHPUnit\Framework\TestCase;

class CourseDeleteTest extends TestCase
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

    public function testDeleteCourse()
    {
        // Test data
        $courseId = 1;

        // Configure mocks
        $this->mockPDO->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM rozvrh WHERE id = :id')
            ->willReturn($this->mockStmt);

        // bindParam call should return true
        $this->mockStmt->expects($this->once())
            ->method('bindParam')
            ->with(':id', $courseId)
            ->willReturn(true);

        $this->mockStmt->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        // Call the method being tested
        $result = $this->course->deleteCourse($courseId);

        // Assert the result
        $this->assertTrue($result);
    }

    public function testDeleteCourseFailed()
    {
        // Test data
        $courseId = 1;

        // Configure mocks for a failed deletion
        $this->mockPDO->expects($this->once())
            ->method('prepare')
            ->with('DELETE FROM rozvrh WHERE id = :id')
            ->willReturn($this->mockStmt);

        // bindParam call should return true
        $this->mockStmt->expects($this->once())
            ->method('bindParam')
            ->with(':id', $courseId)
            ->willReturn(true);

        $this->mockStmt->expects($this->once())
            ->method('execute')
            ->willReturn(false);

        // Call the method being tested
        $result = $this->course->deleteCourse($courseId);

        // Assert the result
        $this->assertFalse($result);
    }
}