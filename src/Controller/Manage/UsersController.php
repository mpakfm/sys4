<?php
/**
 * Created by PhpStorm
 * Project: newspaper
 * User:    mpak
 * Date:    18.08.2022
 * Time:    3:01
 */

namespace App\Controller\Manage;

use App\Controller\BaseController;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Response\ListCounter;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UsersController extends BaseController
{
    /** @var int */
    public $defaultLimit = 20;
    /** @var int */
    public $defaultOffset = 0;
    /** @var string[] */
    public $defaultOrder = [
        'id' => 'asc'
    ];
    /** @var string[] */
    public $accessOrder = ['id', 'name', 'last_name'];
    /** @var int */
    public $limit;
    /** @var int */
    public $offset;
    /** @var string[] */
    public $order;

    /**
     * @Route("/manage/user/list", name="app_manage_user_list")
     */
    public function index(Request $request, UserRepository $repository): Response
    {
        $this->preLoad($request);
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException('Access Denied.');
        }

        $query   = null;
        $counter = new ListCounter();

        $sort  = $request->get('sort');
        $order = $request->get('order');
        if ($sort && !empty($sort)) {
            $this->order[$sort] = ($order == 'desc' ? 'desc' : 'asc');
        }

        $this->limit  = $request->get('limit') ?? $this->defaultLimit;
        $this->offset = $request->get('offset') ?? $this->defaultOffset;
        $this->limit  = (int) $this->limit;
        $this->offset = (int) $this->offset;
        $this->order  = $this->order ?? $this->defaultOrder;

        if (!empty($request->get('query'))) {
            $query    = $request->get('query');
            $search   = $repository->searchByNames($request->get('query'), $this->order, $this->limit == 0 ? null : $this->limit, $this->offset);
            $elements = $search['list'];

            $counter->allItems  = $search['all'];
            $counter->pageItems = $search['on_page'];
        } else {
            $criteria = [];
            $elements = $repository->findBy($criteria, $this->order, $this->limit == 0 ? null : $this->limit, $this->offset);

            $counter->allItems  = $repository->getCount($criteria);
            $counter->pageItems = count($elements);
        }

        $counter->limit  = $this->limit;
        $counter->offset = $this->offset;

        $counter->setPages($request);

        return $this->baseRender('manage/users/index.html.twig', [
            'query'    => $query,
            'elements' => $elements,
            'counter'  => $counter,
            'order'    => $order,
            'sort'     => $sort,
            'menu' => [
                'pount' => 'users'
            ]
        ]);
    }

    /**
     * @Route("/manage/user/edit/{id}", name="app_manage_user_edit")
     */
    public function edit(int $id, Request $request, UserRepository $repository, ValidatorInterface $validator, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $this->preLoad($request);

        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException('Access Denied.');
        }
        if (!$id) {
            throw new NotFoundHttpException('Пользователь не найден');
        }
        $actionUser = $this->getUser();
        $user       = $repository->find($id);
        if (!$user) {
            throw new NotFoundHttpException('Пользователь не найден');
        }
        $form   = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $errors = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            if ($password) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword($user, $password)
                );
            }

            $errors = $validator->validate($user);
            if (!count($errors)) {
                $repository->add($user, true);
                return $this->redirectToRoute('app_manage_user_list');
            }
        }

        return $this->baseRenderForm('manage/users/edit.html.twig', [
            'menu' => [
                'pount' => 'users'
            ],
            'errors' => $errors,
            'item'   => $user,
            'form'   => $form,
        ]);
    }

    /**
     * @Route("/manage/user/copy/{id}", name="app_manage_user_copy")
     */
    public function copy(int $id, Request $request, UserRepository $repository, ValidatorInterface $validator, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $this->preLoad($request);

        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException('Access Denied.');
        }
        if (!$id) {
            throw new NotFoundHttpException('Пользователь не найден');
        }
        $actionUser = $this->getUser();
        $copyuser   = $repository->find($id);
        $user       = new User();
        if (!$copyuser) {
            throw new NotFoundHttpException('Пользователь не найден');
        }
        $form   = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $errors = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            if ($password) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword($user, $password)
                );
            }

            $errors = $validator->validate($user);
            if (!count($errors)) {
                $repository->add($user, true);
                return $this->redirectToRoute('app_manage_user_list');
            }
        }

        return $this->baseRenderForm('manage/users/copy.html.twig', [
            'menu' => [
                'pount' => 'users'
            ],
            'errors'  => $errors,
            'item'    => $copyuser,
            'form'    => $form,
        ]);
    }

    /**
     * @Route("/manage/user/create", name="app_manage_user_create")
     */
    public function create(Request $request, UserRepository $repository, ValidatorInterface $validator, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $this->preLoad($request);

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $errors = null;

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();

            $password = $form->get('password')->getData();
            $user->setPassword(
                $userPasswordHasher->hashPassword($user, $password)
            );
            $errors = $validator->validate($user);
            if (!count($errors)) {
                $repository->add($user, true);
                return $this->redirectToRoute('app_manage_user_list');
            }
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            /** @var FormErrorIterator $errors */
            $errors = $form->getErrors(true, false);
        }

        return $this->baseRenderForm('manage/users/create.html.twig', [
            'menu' => [
                'pount' => 'users'
            ],
            'errors'  => $errors,
            'form'    => $form,
            'item'    => $user,
        ]);
    }

    /**
     * @Route("/manage/user/delete/{id}", name="app_manage_user_delete")
     */
    public function delete(int $id, Request $request, UserRepository $repository): Response
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException('Access Denied.');
        }
        if (!$id) {
            throw new NotFoundHttpException('Пользователь не найден');
        }
        $actionUser = $this->getUser();
        $user       = $repository->find($id);
        if (!$user) {
            throw new NotFoundHttpException('Пользователь не найден');
        }
        if ($actionUser->getId() == $user->getId()) {
            throw new AccessDeniedException('Access Denied.');
        }
        $repository->remove($user, true);
        return $this->redirectToRoute('app_manage_user_list');
    }

    /**
     * @Route("/manage/user/delete_bulk", name="app_manage_user_delete_bulk", methods={"POST"})
     */
    public function deleteBulk(Request $request, UserRepository $repository)
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException('Access Denied.');
        }
        $ids = $request->request->get('ids');
        $repository->deleteBulk($ids);
        return $this->json([
            'count'  => count($ids),
        ]);
    }

    /**
     * @Route("/manage/user/check_email", name="app_manage_user_check_email", methods={"POST"})
     */
    public function checkEmail(Request $request, UserRepository $repository)
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException('Access Denied.');
        }
        /** @var \App\Entity\User[] $result */
        $result = $repository->findBy(['email' => $request->get('email')]);
        return $this->json([
            'origin' => $request->get('email'),
            'count'  => count($result),
        ]);
    }
}
