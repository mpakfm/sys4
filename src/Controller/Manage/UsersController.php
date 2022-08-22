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
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UsersController extends BaseController
{
    /**
     * @Route("/manage/users", name="app_manage_users")
     */
    public function index(Request $request, UserRepository $repository): Response
    {
        $this->preLoad($request);
        //$hasAccess = $this->isGranted('ROLE_ADMIN');
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');
        $criteria = [];
        $order    = ['id' => 'asc'];
        $limit    = 20;
        $offset   = 0;
        $elements = $repository->findBy($criteria, $order, $limit, $offset);
        return $this->baseRender('manage/users/index.html.twig', [
            'elements' => $elements,
            'menu' => [
                'pount' => 'users'
            ]
        ]);
    }

    /**
     * @Route("/manage/user", name="app_manage_user")
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
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $errors = $validator->validate($user);
            if (!count($errors)) {
                $repository->add($user, true);
                return $this->redirectToRoute('app_manage_users');
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
        ]);
    }

    /**
     * @Route("/manage/delete/{id}", name="app_manage_delete")
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
        return $this->redirectToRoute('app_manage_users');
    }

}
