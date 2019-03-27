<?php

namespace App\Command;

use App\Entity\Attendee;
use App\Repository\AttendeeRepository;
use App\Repository\UserRepository;
use App\Service\GoogleAdapter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateDemoEventsCommand extends Command
{
    protected static $defaultName = 'app:generate-demo-events';
    /**
     * @var GoogleAdapter
     */
    private $googleAdapter;
    /**
     * @var AttendeeRepository
     */
    private $attendeeRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct($name = null, GoogleAdapter $googleAdapter, AttendeeRepository $attendeeRepository, UserRepository $userRepository)
    {
        parent::__construct($name);
        $this->googleAdapter = $googleAdapter;
        $this->attendeeRepository = $attendeeRepository;
        $this->userRepository = $userRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $eventStartDate = new \DateTime(date('H:i:s', (time() + 60 * 60) - time() % (60 * 60)), new \DateTimeZone('CET'));
        $eventStartDate->modify('+3 days');
        $eventEndDate = new \DateTime('+2 months', new \DateTimeZone('CET'));

        $user = $this->userRepository->find(1);

        $attendees = $this->attendeeRepository->findAll();

        while ($eventStartDate < $eventEndDate) {
            for ($i = 0; $i < 8; $i++) {
                $faker = \Faker\Factory::create();
                $endTime = clone $eventStartDate;
                $endTime->modify('+1 hour');

                shuffle($attendees);
                $eventAttendees = array_chunk($attendees, 5)[0];

                $event = [
                    'summary' => $faker->text(40),
                    'location' => $faker->address,
                    'description' => $faker->text(),
                    'start' => [
                        'dateTime' => $eventStartDate->format(DATE_ATOM),
                        'timeZone' => $eventStartDate->getTimezone()->getName(),
                    ],
                    'end' => [
                        'dateTime' => $endTime->format(DATE_ATOM),
                        'timeZone' => $endTime->getTimezone()->getName(),
                    ],
                    'attendees' => array_map(function (Attendee $attendee) {
                        return [
                            'email' => $attendee->getEmail(),
                            'displayName' => $attendee->getName(),
                        ];
                    }, $eventAttendees),

                ];

                $this->googleAdapter->createEvent($event, $user);

                $eventStartDate = clone $endTime;
            }

            $eventStartDate = $eventStartDate->modify('+16 hours');
        }
    }
}
