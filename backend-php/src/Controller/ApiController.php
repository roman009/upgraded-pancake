<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\AuthTokenGenerator;
use App\Service\GoogleAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(host="{domain}", defaults={"domain"="%domain%"}, requirements={"domain"="%domain%"})
 */
class ApiController extends AbstractController
{
    public const USE_CACHE = true;

    /**
     * @Route("/", name="api-index")
     */
    public function index(): JsonResponse
    {
        return $this->json('OK');
    }

    /**
     * @Route("/auth-url", methods={"GET", "OPTIONS"}, name="api-google-auth-url")
     * @param Request $request
     * @param GoogleAdapter $googleAdapter
     * @return JsonResponse
     */
    public function getAuthUrl(Request $request, GoogleAdapter $googleAdapter): JsonResponse
    {
        if ($request->getMethod() === Request::METHOD_OPTIONS) {
            return $this->options();
        }

        return $this->jsonWithCors(['auth_url' => $googleAdapter->getAuthUrl()]);
    }

    /**
     * @Route("/callback", methods={"GET", "OPTIONS"}, name="api-google-auth-callback")
     * @param Request $request
     * @param GoogleAdapter $googleAdapter
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function handleGoogleAuthCallback(Request $request, GoogleAdapter $googleAdapter, UserRepository $userRepository): JsonResponse
    {
        if ($request->getMethod() === Request::METHOD_OPTIONS) {
            return $this->options();
        }

        $code = $request->get('code');

        $token = $googleAdapter->fetchAccessTokenWithAuthCode($code);

        $userDetails = $googleAdapter->getUserDetails($token);

        $user = $userRepository->findOneBy(['email' => $userDetails['email']]);
        if (null === $user) {
            $user = (new User)
                ->setEmail($userDetails['email'])
                ->setName($userDetails['name'])
                ->setRoles(['ROLE_USER']);
        }

        $user
            ->setApiToken((new AuthTokenGenerator)())
            ->setGoogleTokenExpiresIn($token['expires_in'])
            ->setGoogleAccessToken($token['access_token'])
            ->setGoogleTokenScope($token['scope'])
            ->setGoogleTokenId($token['id_token'])
            ->setGoogleRefreshToken($token['refresh_token'])
            ->setGoogleTokenCreated($token['created']);

        $userRepository->persistAndFlush($user);

        return $this->jsonWithCors(['X-AUTH-TOKEN' => $user->getApiToken()]);
    }

    /**
     * @Route("/events/{id}", methods={"GET", "OPTIONS"}, name="api-get-event")
     * @param Request $request
     * @param int $id
     * @param GoogleAdapter $googleAdapter
     * @return JsonResponse
     */
    public function getEvent(Request $request, int $id, GoogleAdapter $googleAdapter): JsonResponse
    {
        if ($request->getMethod() === Request::METHOD_OPTIONS) {
            return $this->options();
        }

        $event = $googleAdapter->getEvent($id, self::USE_CACHE);

        return $this->jsonWithCors($event);
    }

    /**
     * @Route("/events/{id}", methods={"PUT", "OPTIONS"}, name="api-end-event")
     * @param Request $request
     * @param int $id
     * @param GoogleAdapter $googleAdapter
     * @return JsonResponse
     * @throws \Exception
     */
    public function endEvent(Request $request, int $id, GoogleAdapter $googleAdapter): JsonResponse
    {
        if ($request->getMethod() === Request::METHOD_OPTIONS) {
            return $this->options();
        }

        $event = $googleAdapter->endEvent($id);

        return $this->jsonWithCors($event);
    }

    /**
     * @Route("/events", methods={"GET", "OPTIONS"}, name="api-get-events")
     * @param Request $request
     * @param GoogleAdapter $googleAdapter
     * @return JsonResponse
     * @throws \Exception
     */
    public function getEvents(Request $request, GoogleAdapter $googleAdapter): JsonResponse
    {
        if ($request->getMethod() === Request::METHOD_OPTIONS) {
            return $this->options();
        }

        $page = $request->get('page', 1);
        $pageSize = 1000;

        $events = $googleAdapter->getEvents($page, $pageSize, self::USE_CACHE);

        return $this->jsonWithCors($events);
    }

    /**
     * @Route("/me", methods={"GET", "OPTIONS"}, name="api-get-me")
     * @param Request $request
     * @return JsonResponse
     */
    public function getMe(Request $request): JsonResponse
    {
        if ($request->getMethod() === Request::METHOD_OPTIONS) {
            return $this->options();
        }

        $user = $this->getUser();

        return $this->jsonWithCors([
            'name' => $user->getName(),
            'email' => $user->getEmail(),
        ]);
    }

    /**
     * @param $data
     * @return JsonResponse
     */
    protected function jsonWithCors($data): JsonResponse
    {
        return $this->json(
            $data,
            Response::HTTP_OK,
            [
                'Access-Control-Allow-Origin' => '*',
                'content-type' => 'application/json',
                'Access-Control-Allow-Headers' => 'Content-Type, X-AUTH-TOKEN',
                'Access-Control-Allow-Methods' => 'OPTIONS, GET, POST, PUT',
            ]
        );
    }

    protected function options(): JsonResponse
    {
        return $this->jsonWithCors(null);
    }
}
