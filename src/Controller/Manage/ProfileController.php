<?php

namespace App\Controller\Manage;

use App\Controller\BaseController;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProfileController extends BaseController
{
    /**
     * @Route("/manage/profile", name="app_manage_profile")
     */
    public function index(Request $request, UserRepository $repository, ValidatorInterface $validator, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $this->preLoad($request);

        $currUser = $this->getUser();
        $user     = $repository->findBy(['email' => $currUser->getUserIdentifier()])[0];

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
                return $this->redirectToRoute('app_manage_profile');
            }
        }

        return $this->baseRenderForm('manage/profile/index.html.twig', [
            'menu' => [
                'pount' => 'users'
            ],
            'errors'  => $errors,
            'item'    => $user,
            'form'    => $form,
        ]);
    }
}
