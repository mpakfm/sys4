<?php

namespace App\Controller;

use App\Entity\StatClientConnections;
use App\Repository\StatClientConnectionsRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CounterController extends BaseController
{
    /**
     * @Route("/counter", name="app_counter")
     */
    public function index(Request $request, UserRepository $repository, StatClientConnectionsRepository $statClientConnectionsRepository): JsonResponse
    {
        $cookie    = $request->cookies->get('client');
        $userToken = $this->getUser();
        if ($userToken) {
            $user = $repository->findOneBy(['email' => $userToken->getUserIdentifier()]);
        }
        $entity = new StatClientConnections();
        $entity
            ->setClientId($cookie)
            ->setUserId($userToken && $user ? $user->getId() : null)
            ->setDateTime(new \DateTime())
            ->setRemoteAddr($request->server->get('REMOTE_ADDR'))
            ->setUserAgent($request->server->get('HTTP_USER_AGENT'))
            ->setUrl($request->request->get('url'))
            ->setPage($request->request->get('page'))
            ->setPing($request->request->get('ping') ? (int) $request->request->get('ping') : 0);
        $statClientConnectionsRepository->add($entity, true);
        return $this->json(true);
    }
}
