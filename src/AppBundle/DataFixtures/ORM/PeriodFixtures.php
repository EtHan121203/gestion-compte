<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\FixturesConstants;
use AppBundle\Entity\Period;
use AppBundle\Entity\PeriodPosition;
use DateInterval;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class PeriodFixtures extends Fixture implements OrderedFixtureInterface, FixtureGroupInterface
{

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager)
    {

        $enabled_jobs_count = FixturesConstants::ENABLED_JOBS_COUNT;
        $adminsCount = FixturesConstants::ADMINS_COUNT;

        // Only generate periods for Monday to Saturday (0-5)
        // Sunday (6) is excluded - store is closed (SCRUM-16)
        for ($i = 0; $i < 6; $i++) {

            $period = new Period();

            $randomTime = rand(9, 18);
            $startDate = new DateTime();
            $startDate->setTime($randomTime, 0);
            $endDate = (clone $startDate)->add(new DateInterval('PT' . 2 . 'H'));

            $period->setStart($startDate);
            $period->setEnd($endDate);

            $period->setDayOfWeek($i); // 0=Monday through 5=Saturday

            $randJobId = rand(1, $enabled_jobs_count);
            $job = $this->getReference('job_' . $randJobId);
            $period->setJob($job);


            $weekCycles = ["A", "B", "C", "D"];
            for($j = 0; $j < 4; $j++) {

                $weekCycle = $weekCycles[$j];

                for($k = 0; $k < rand(2,5); $k++) {
                    $periodPosition = new PeriodPosition();
                    $periodPosition->setPeriod($period);
                    $periodPosition->setFormation($this->getReference('formation_' . $randJobId));
                    $periodPosition->setWeekCycle($weekCycle);
                    $period->addPosition($periodPosition);
                }

            }

            $manager->persist($period);
            $manager->persist($periodPosition);
        }



        $manager->flush();

        echo "6 periods per week (Monday-Saturday) with random number of positions created\n";
    }

    public static function getGroups(): array
    {
        return ['period'];
    }

    public function getOrder(): int
    {
        return 13;
    }
}
