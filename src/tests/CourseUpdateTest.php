<?php
require_once __DIR__.'/../php/Course.php';

use PHPUnit\Framework\TestCase;

class CourseUpdateTest extends TestCase
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

    public function testUpdateCourse()
    {
        // Test data
        $courseId = 1;
        $courseData = [
            'den' => 'Po',
            'cas_od' => '14:00:00',
            'cas_do' => '15:50:00',
            'typ_akcie' => 'Prednáška',
            'nazov_akcie' => 'Updated Course',
            'miestnost' => 'D123',
            'vyucujuci' => 'Updated Teacher'
        ];

        // First mock for checkAvailableUpdate
        $checkStmt = $this->createMock(PDOStatement::class);

        // All bindParam calls should return true
        $checkStmt->expects($this->exactly(4))
            ->method('bindParam')
            ->willReturn(true);

        $checkStmt->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $checkStmt->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([]); // No conflicts

        // Second mock for update
        $updateStmt = $this->createMock(PDOStatement::class);

        // All bindParam calls should return true
        $updateStmt->expects($this->exactly(8))
            ->method('bindParam')
            ->willReturn(true);

        $updateStmt->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        // PDO should prepare two different statements
        $this->mockPDO->expects($this->exactly(2))
            ->method('prepare')
            ->willReturnCallback(function ($sql) use ($checkStmt, $updateStmt) {
                if (strpos($sql, 'SELECT * FROM rozvrh WHERE id !=') !== false) {
                    return $checkStmt;
                } else if (strpos($sql, 'UPDATE rozvrh SET') !== false) {
                    return $updateStmt;
                }
                return $this->createMock(PDOStatement::class);
            });

        // Call the method being tested
        $result = $this->course->updateCourse($courseId, $courseData);

        // Assert the result
        $this->assertTrue($result);
    }

    public function testUpdateCourseTimeOccupied()
    {
        // Test data
        $courseId = 1;
        $courseData = [
            'den' => 'Po',
            'cas_od' => '08:30:00',  // This time overlaps with existing course
            'cas_do' => '10:00:00',
            'typ_akcie' => 'Prednáška',
            'nazov_akcie' => 'Updated Course',
            'miestnost' => 'D123',
            'vyucujuci' => 'Updated Teacher'
        ];

        // Configure mocks for checkAvailableUpdate to return occupied
        $this->mockPDO->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStmt);

        // bindParam calls should return true
        $this->mockStmt->expects($this->exactly(4))
            ->method('bindParam')
            ->willReturn(true);

        $this->mockStmt->expects($this->once())
            ->method('execute');

        $this->mockStmt->expects($this->once())
            ->method('fetchAll')
            ->willReturn([['id' => 2]]); // Return a conflicting course

        // Call the method being tested
        $result = $this->course->updateCourse($courseId, $courseData);

        // Assert the result
        $this->assertFalse($result);
    }

    public function testUpdateCourseExecutionFailure()
    {
        // Test data
        $courseId = 1;
        $courseData = [
            'den' => 'Po',
            'cas_od' => '14:00:00',
            'cas_do' => '15:50:00',
            'typ_akcie' => 'Prednáška',
            'nazov_akcie' => 'Updated Course',
            'miestnost' => 'D123',
            'vyucujuci' => 'Updated Teacher'
        ];

        // First mock for checkAvailableUpdate - passes availability check
        $checkStmt = $this->createMock(PDOStatement::class);

        // All bindParam calls should return true
        $checkStmt->expects($this->exactly(4))
            ->method('bindParam')
            ->willReturn(true);

        $checkStmt->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $checkStmt->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([]); // No conflicts

        // Second mock for update - will fail on execute
        $updateStmt = $this->createMock(PDOStatement::class);

        // All bindParam calls should return true
        $updateStmt->expects($this->exactly(8))
            ->method('bindParam')
            ->willReturn(true);

        $updateStmt->expects($this->once())
            ->method('execute')
            ->willReturn(false); // This simulates a SQL execution error

        // PDO should prepare two different statements
        $this->mockPDO->expects($this->exactly(2))
            ->method('prepare')
            ->willReturnCallback(function ($sql) use ($checkStmt, $updateStmt) {
                if (strpos($sql, 'SELECT * FROM rozvrh WHERE id !=') !== false) {
                    return $checkStmt;
                } else if (strpos($sql, 'UPDATE rozvrh SET') !== false) {
                    return $updateStmt;
                }
                return $this->createMock(PDOStatement::class);
            });

        // Call the method being tested
        $result = $this->course->updateCourse($courseId, $courseData);

        // Assert that the result is false due to failed execution
        $this->assertFalse($result);
    }
}