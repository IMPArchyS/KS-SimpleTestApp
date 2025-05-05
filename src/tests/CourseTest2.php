<?php
require_once __DIR__.'/../php/Course.php';

use PHPUnit\Framework\TestCase;

class CourseTest2 extends TestCase
{
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