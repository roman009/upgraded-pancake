<?php

namespace App\Command;

use App\Entity\Attendee;
use App\Repository\AttendeeRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateDemoAttendeesCommand extends Command
{
    protected static $defaultName = 'app:generate-demo-attendees';
    /**
     * @var AttendeeRepository
     */
    private $attendeeRepository;

    public function __construct($name = null, AttendeeRepository $attendeeRepository)
    {
        parent::__construct($name);
        $this->attendeeRepository = $attendeeRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 100; $i++) {
            $cost = rand(20, 80);
            $revenue = rand(40, 200);
            while ($revenue < $cost) {
                $revenue = rand(40, 200);
            }

            $attendee = (new Attendee)
                ->setName($faker->name)
                ->setEmail($faker->safeEmail)
                ->setCost($cost)
                ->setRevenue($revenue);

            $this->attendeeRepository->persistAndFlush($attendee);
        }
    }
}
