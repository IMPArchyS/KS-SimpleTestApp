<?php
require_once __DIR__.'/../php/Course.php';

use PHPUnit\Framework\TestCase;

class CourseBasicTest extends TestCase
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

    public function testCourseClassExists()
    {
        $this->assertTrue(class_exists('Course'));
    }

    public function testConstructorAcceptsPDO()
    {
        $mockPDO = $this->createMock(PDO::class);
        $course = new Course($mockPDO);
        $this->assertInstanceOf(Course::class, $course);
    }
}