<?php
require_once __DIR__.'/../php/Course.php';

use PHPUnit\Framework\TestCase;

class CourseCreateTest extends TestCase
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

    public function testAddCourseSuccess()
    {
        // Test data
        $courseData = [
            'den' => 'Po',
            'cas_od' => '14:00:00',
            'cas_do' => '15:50:00',
            'typ_akcie' => 'Prednáška',
            'nazov_akcie' => 'New Course',
            'miestnost' => 'C789',
            'vyucujuci' => 'Test Teacher'
        ];

        // First mock for checkAvailable
        $checkStmt = $this->createMock(PDOStatement::class);

        // All bindParam calls should return true
        $checkStmt->expects($this->exactly(3))
            ->method('bindParam')
            ->willReturn(true);

        $checkStmt->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $checkStmt->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([]); // No conflicts

        // Second mock for insert
        $insertStmt = $this->createMock(PDOStatement::class);

        // All bindParam calls should return true
        $insertStmt->expects($this->exactly(7))
            ->method('bindParam')
            ->willReturn(true);

        $insertStmt->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        // PDO should prepare two different statements
        $this->mockPDO->expects($this->exactly(2))
            ->method('prepare')
            ->willReturnCallback(function ($sql) use ($checkStmt, $insertStmt) {
                if (strpos($sql, 'SELECT * FROM rozvrh WHERE den =') !== false) {
                    return $checkStmt;
                } else if (strpos($sql, 'INSERT INTO rozvrh') !== false) {
                    return $insertStmt;
                }
                return $this->createMock(PDOStatement::class);
            });

        // Call the method being tested
        $result = $this->course->addCourse($courseData);

        // Assert the result
        $this->assertTrue($result);
    }

    public function testAddCourseTimeOccupied()
    {
        // Test data
        $courseData = [
            'den' => 'Po',
            'cas_od' => '08:30:00',  // This time overlaps with existing course
            'cas_do' => '10:00:00',
            'typ_akcie' => 'Cvičenie',
            'nazov_akcie' => 'New Course',
            'miestnost' => 'C789',
            'vyucujuci' => 'Test Teacher'
        ];

        // Configure mocks for checkAvailable to return occupied
        $this->mockPDO->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStmt);

        // bindParam calls should return true
        $this->mockStmt->expects($this->exactly(3))
            ->method('bindParam')
            ->willReturn(true);

        $this->mockStmt->expects($this->once())
            ->method('execute');

        $this->mockStmt->expects($this->once())
            ->method('fetchAll')
            ->willReturn([['id' => 1]]); // Return a conflicting course

        // Call the method being tested
        $result = $this->course->addCourse($courseData);

        // Assert the result
        $this->assertFalse($result);
    }

    public function testAddCourseInvalidData()
    {
        // Test data with missing required field
        $courseData = [
            'den' => 'Po',
            'cas_od' => '14:00:00',
            'cas_do' => '15:50:00',
            // Missing 'typ_akcie'
            'nazov_akcie' => 'New Course',
            'miestnost' => 'C789',
            'vyucujuci' => 'Test Teacher'
        ];

        // Configure mock for checkAvailable
        $this->mockPDO->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStmt);

        // bindParam calls should return true
        $this->mockStmt->expects($this->exactly(3))
            ->method('bindParam')
            ->willReturn(true);

        $this->mockStmt->expects($this->once())
            ->method('execute');

        $this->mockStmt->expects($this->once())
            ->method('fetchAll')
            ->willReturn([]);

        // Call the method being tested
        $result = $this->course->addCourse($courseData);

        // Assert the result
        $this->assertFalse($result);
    }

    public function testAddCourseExecutionFailure()
    {
        // Test data with all required fields
        $courseData = [
            'den' => 'Po',
            'cas_od' => '14:00:00',
            'cas_do' => '15:50:00',
            'typ_akcie' => 'Prednáška',
            'nazov_akcie' => 'New Course',
            'miestnost' => 'C789',
            'vyucujuci' => 'Test Teacher'
        ];

        // First mock for checkAvailable - allows the operation to proceed
        $checkStmt = $this->createMock(PDOStatement::class);

        // All bindParam calls should return true
        $checkStmt->expects($this->exactly(3))
            ->method('bindParam')
            ->willReturn(true);

        $checkStmt->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $checkStmt->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([]); // No conflicts

        // Second mock for insert - but this time the execute will fail
        $insertStmt = $this->createMock(PDOStatement::class);

        // All bindParam calls should return true
        $insertStmt->expects($this->exactly(7))
            ->method('bindParam')
            ->willReturn(true);

        $insertStmt->expects($this->once())
            ->method('execute')
            ->willReturn(false); // This simulates a SQL execution error

        // PDO should prepare two different statements
        $this->mockPDO->expects($this->exactly(2))
            ->method('prepare')
            ->willReturnCallback(function ($sql) use ($checkStmt, $insertStmt) {
                if (strpos($sql, 'SELECT * FROM rozvrh WHERE den =') !== false) {
                    return $checkStmt;
                } else if (strpos($sql, 'INSERT INTO rozvrh') !== false) {
                    return $insertStmt;
                }
                return $this->createMock(PDOStatement::class);
            });

        // Call the method being tested
        $result = $this->course->addCourse($courseData);

        // Assert that the result is false due to failed execution
        $this->assertFalse($result);
    }
}