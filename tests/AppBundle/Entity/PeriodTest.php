<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Period;
use AppBundle\Entity\Job;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Test for Period entity validation
 * 
 * SCRUM-16: Ensure Sunday periods cannot be created
 */
class PeriodTest extends KernelTestCase
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::$container->get('validator');
    }

    /**
     * Test that periods can be created for Monday through Saturday (0-5)
     */
    public function testValidDaysOfWeek()
    {
        $validDays = [0, 1, 2, 3, 4, 5]; // Monday to Saturday
        
        foreach ($validDays as $dayOfWeek) {
            $period = $this->createPeriod($dayOfWeek);
            $errors = $this->validator->validate($period);
            
            $this->assertCount(
                0, 
                $errors, 
                "Day $dayOfWeek should be valid but got errors: " . (string) $errors
            );
        }
    }

    /**
     * Test that periods cannot be created for Sunday (6)
     * 
     * @dataProvider sundayDataProvider
     */
    public function testSundayIsBlocked($dayOfWeek)
    {
        $period = $this->createPeriod($dayOfWeek);
        $errors = $this->validator->validate($period);
        
        $this->assertGreaterThan(
            0, 
            count($errors), 
            "Sunday (day $dayOfWeek) should be blocked"
        );
        
        // Check that the error is on the dayOfWeek field
        $this->assertEquals('dayOfWeek', $errors[0]->getPropertyPath());
        
        // Check error message mentions that store is closed
        $this->assertStringContainsString('fermé', $errors[0]->getMessage());
    }

    public function sundayDataProvider()
    {
        return [[6]]; // Sunday
    }

    /**
     * Test that invalid day numbers are handled
     */
    public function testInvalidDayNumbers()
    {
        $invalidDays = [-1, 7, 8, 100];
        
        foreach ($invalidDays as $dayOfWeek) {
            $period = $this->createPeriod($dayOfWeek);
            $errors = $this->validator->validate($period);
            
            // These should either be blocked by Sunday validation or other constraints
            // At minimum, day 7 and higher should fail
            if ($dayOfWeek >= 7) {
                $this->assertGreaterThan(
                    0,
                    count($errors),
                    "Day $dayOfWeek should be invalid"
                );
            }
        }
    }

    /**
     * Test that end time must be after start time
     */
    public function testEndTimeAfterStartTime()
    {
        $period = new Period();
        $period->setDayOfWeek(0); // Monday
        
        $start = new DateTime('10:00');
        $end = new DateTime('08:00'); // Before start!
        
        $period->setStart($start);
        $period->setEnd($end);
        
        // Create a mock job
        $job = new Job();
        $job->setName('Test Job');
        $job->setEnabled(true);
        $period->setJob($job);
        
        $errors = $this->validator->validate($period);
        
        $this->assertGreaterThan(
            0,
            count($errors),
            "End time before start time should be invalid"
        );
        
        // Find the error related to end time
        $endTimeError = null;
        foreach ($errors as $error) {
            if ($error->getPropertyPath() === 'end') {
                $endTimeError = $error;
                break;
            }
        }
        
        $this->assertNotNull($endTimeError, "Should have an error on 'end' field");
        $this->assertStringContainsString('après', $endTimeError->getMessage());
    }

    /**
     * Test that equal start and end times are invalid
     */
    public function testEndTimeEqualToStartTime()
    {
        $period = new Period();
        $period->setDayOfWeek(0); // Monday
        
        $time = new DateTime('10:00');
        
        $period->setStart($time);
        $period->setEnd(clone $time); // Same time
        
        // Create a mock job
        $job = new Job();
        $job->setName('Test Job');
        $job->setEnabled(true);
        $period->setJob($job);
        
        $errors = $this->validator->validate($period);
        
        $this->assertGreaterThan(
            0,
            count($errors),
            "End time equal to start time should be invalid"
        );
    }

    /**
     * Test valid period with correct start and end times
     */
    public function testValidPeriod()
    {
        $period = new Period();
        $period->setDayOfWeek(0); // Monday
        
        $start = new DateTime('09:00');
        $end = new DateTime('12:00');
        
        $period->setStart($start);
        $period->setEnd($end);
        
        // Create a mock job
        $job = new Job();
        $job->setName('Test Job');
        $job->setEnabled(true);
        $period->setJob($job);
        
        $errors = $this->validator->validate($period);
        
        $this->assertCount(
            0,
            $errors,
            "Valid period should have no errors: " . (string) $errors
        );
    }

    /**
     * Helper method to create a basic period for testing
     */
    private function createPeriod($dayOfWeek): Period
    {
        $period = new Period();
        $period->setDayOfWeek($dayOfWeek);
        
        $start = new DateTime('09:00');
        $end = new DateTime('12:00');
        
        $period->setStart($start);
        $period->setEnd($end);
        
        // Create a minimal job
        $job = new Job();
        $job->setName('Test Job');
        $job->setEnabled(true);
        $period->setJob($job);
        
        return $period;
    }
}
