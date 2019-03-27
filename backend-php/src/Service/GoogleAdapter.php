<?php

namespace App\Service;

use App\Entity\GoogleCalendarEvent;
use App\Entity\GoogleCalendarEventAttendee;
use App\Entity\User;
use App\Repository\AttendeeRepository;
use App\Repository\GoogleCalendarEventAttendeeRepository;
use App\Repository\GoogleCalendarEventRepository;
use App\Repository\UserRepository;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Symfony\Component\Routing\RouterInterface;

class GoogleAdapter
{
    public const DEFAULT_CALENDAR_ID = '463l3eu29av0tghq8bcg4n74jk@group.calendar.google.com';

    /**
     * @var \Google_Client
     */
    private $client;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var GoogleCalendarEventRepository
     */
    private $googleCalendarEventRepository;
    /**
     * @var AttendeeRepository
     */
    private $attendeeRepository;
    /**
     * @var GoogleCalendarEventAttendeeRepository
     */
    private $googleCalendarEventAttendeeRepository;

    public function __construct(
        \Google_Client $client,
        RouterInterface $router,
        UserRepository $userRepository,
        GoogleCalendarEventRepository $googleCalendarEventRepository,
        AttendeeRepository $attendeeRepository,
        GoogleCalendarEventAttendeeRepository $googleCalendarEventAttendeeRepository
    ) {
        $this->client = $client;
        $this->router = $router;
        $this->userRepository = $userRepository;
        $this->googleCalendarEventRepository = $googleCalendarEventRepository;
        $this->attendeeRepository = $attendeeRepository;
        $this->googleCalendarEventAttendeeRepository = $googleCalendarEventAttendeeRepository;
    }

    public function getAuthUrl(): string
    {
        $this->init();

        return $this->client->createAuthUrl();
    }

    public function fetchAccessTokenWithAuthCode(string $code): array
    {
        $this->init();

        return $this->client->fetchAccessTokenWithAuthCode($code);
    }

    public function getUserDetails(array $token)
    {
        $this->init();

        $this->client->setAccessToken($token);

        return $this->client->verifyIdToken($token['id_token']);
    }

    protected function init(): void
    {
        $redirectUri = 'https://moneywaster.test.buzila.ro' . $this->router->generate('api-google-auth-callback');
        $this->client->setRedirectUri($redirectUri);
        $this->client->setAccessType('offline');
        $this->client->setState('offline');
        $this->client->setApplicationName('MoneyWaster');
        $this->client->setApprovalPrompt('force');
        $this->client->setScopes([
            \Google_Service_Oauth2::USERINFO_EMAIL,
            \Google_Service_Oauth2::USERINFO_PROFILE,
            \Google_Service_Calendar::CALENDAR,
            \Google_Service_Calendar::CALENDAR_EVENTS,
        ]);
    }


    /**
     * @param int $id
     * @param bool $useCache
     * @return GoogleCalendarEvent
     */
    public function getEvent(int $id, bool $useCache = true): GoogleCalendarEvent
    {
        return $this->googleCalendarEventRepository->find($id);
    }

    /**
     * @param int $id
     * @return GoogleCalendarEvent
     * @throws \Exception
     */
    public function endEvent(int $id): GoogleCalendarEvent
    {
        $event = $this->getEvent($id);
        $event->setRealEndTime(new \DateTime);

        $this->googleCalendarEventRepository->persistAndFlush($event);

        return $event;
    }

    /**
     * @param int $page
     * @param int $pageSize
     * @param bool $useCache
     * @return array|mixed
     * @throws \Exception
     */
    public function getEvents(int $page = 1, int $pageSize = 20, bool $useCache = true)
    {
        $eventList = [];

        if ($useCache) {
            $eventList = $this->googleCalendarEventRepository->findNextEvents($pageSize, ($page - 1) * $pageSize);

            return $eventList;
        }

        $this->init();

        $service = new Google_Service_Calendar($this->client);

        $optParams = [
            'timeZone' => 'CET',
            'timeMin' => (new \DateTime('-10 days'))->format(DATE_ATOM),
            'timeMax' => (new \DateTime('+50 days'))->format(DATE_ATOM),
        ];

        try {
            $response = $service->events->listEvents(self::DEFAULT_CALENDAR_ID, $optParams);
        } catch (\Google_Exception $exception) {
            $exception = json_decode($exception->getMessage(), true);
            throw new \Exception($exception['error']['message']);
        }

        /** @var Google_Service_Calendar_Event $event */
        foreach ($response->getItems() as $event) {
            $googleCalendarEvent = $this->googleCalendarEventRepository->findOneBy(['googleEventId' => $event->getId()]);
            if (null === $googleCalendarEvent) {
                $googleCalendarEvent = (new GoogleCalendarEvent)
                    ->setGoogleEventId($event->getId());
            }
            $googleCalendarEvent
                ->setDescription($event->getDescription())
                ->setSummary($event->getSummary())
                ->setEnd(new \DateTime($event->getEnd()->getDateTime()))
                ->setStart(new \DateTime($event->getStart()->getDateTime()));

            $this->googleCalendarEventRepository->persistAndFlush($googleCalendarEvent);

            $eventList[] = $googleCalendarEvent;
        }

        usort($eventList, function (GoogleCalendarEvent $a, GoogleCalendarEvent $b) {
            return $a->getStart() > $b->getStart();
        });

        return $eventList;
    }

    /**
     * @param array $event
     * @param User $user
     * @throws \Exception
     */
    public function createEvent(array $event, User $user): void
    {
        $this->init();
        $this->auth($user);

        $service = new \Google_Service_Calendar($this->client);

        $event = new \Google_Service_Calendar_Event($event);
        $event = $service->events->insert(self::DEFAULT_CALENDAR_ID, $event);

        $googleCalendarEvent = (new GoogleCalendarEvent)
            ->setDescription($event->getDescription())
            ->setSummary($event->getSummary())
            ->setEnd(new \DateTime($event->getEnd()->getDateTime()))
            ->setStart(new \DateTime($event->getStart()->getDateTime()))
            ->setGoogleEventId($event->getId())
        ;
        $this->googleCalendarEventRepository->persistAndFlush($googleCalendarEvent);

        foreach ($event['attendees'] as $attendee) {
            $attendee = $this->attendeeRepository->findOneBy(['email' => $attendee['email']]);
            $googleCalendarEventAttendee = (new GoogleCalendarEventAttendee)
                ->setAttendee($attendee)
                ->setGoogleCalendarEvent($googleCalendarEvent)
            ;
            $this->googleCalendarEventAttendeeRepository->persistAndFlush($googleCalendarEventAttendee);
        }
    }

    /**
     * @param User $user
     */
    protected function auth(User $user): void
    {
        $this->client->setAccessToken(json_encode([
            'access_token' => $user->getGoogleAccessToken(),
            'refresh_token' => $user->getGoogleRefreshToken(),
            'expires_in' => $user->getGoogleTokenExpiresIn(),
            'id_token' => $user->getGoogleTokenId(),
            'created' => $user->getGoogleTokenCreated(),
        ]));

        if (!$this->client->isAccessTokenExpired()) {
            return;
        }

        $token = $this->client->fetchAccessTokenWithRefreshToken();
        $user
            ->setApiToken((new AuthTokenGenerator)())
            ->setGoogleTokenExpiresIn($token['expires_in'])
            ->setGoogleAccessToken($token['access_token'])
            ->setGoogleTokenScope($token['scope'])
            ->setGoogleTokenId($token['id_token'])
            ->setGoogleRefreshToken($token['refresh_token'])
            ->setGoogleTokenCreated($token['created']);

        $this->userRepository->persistAndFlush($user);
    }
}
