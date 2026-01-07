<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\FixturesConstants;
use AppBundle\Entity\Event;
use AppBundle\Entity\EventKind;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class EventFixtures extends Fixture implements OrderedFixtureInterface, FixtureGroupInterface
{

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager)
    {

        $eventTitles = FixturesConstants::EVENT_TITLES;
        $eventCounts = FixturesConstants::EVENTS_COUNT;
        $eventDescriptions = FixturesConstants::EVENT_DESCRIPTIONS;

        for ($i = 0; $i < $eventCounts; $i++) {

            $event = new Event();
            $event->setDate(new DateTime('+' . rand(1, 30) . ' days'));
            $event->setTitle($eventTitles[$i]);
            $event->setDescription($eventDescriptions[$i]);

            $event->setNeedProxy((bool)rand(0, 1));
            $event->setAnonymousProxy((bool)rand(0, 1));

            $this->setReference('event_' . ($i+1), $event);

            $manager->persist($event);

        }

        $manager->flush();

        echo $eventCounts . " events created\n";
    }

    public static function getGroups(): array
    {
        return ['period'];
    }

    public function getOrder(): int
    {
        return 9;
    }

}
