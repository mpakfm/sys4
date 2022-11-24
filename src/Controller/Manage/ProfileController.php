<?php

namespace App\Controller\Manage;

use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\ContentManager;
use Mpakfm\Printu;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProfileController extends AdminController
{
    /**
     * @Route("/manage/profile", name="app_manage_profile")
     */
    public function index(Request $request,
        UserRepository $repository,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $userPasswordHasher,
        SluggerInterface $slugger,
        ContentManager $contentManager): Response
    {
        $this->preLoad($request, $contentManager);

        $currUser = $this->getUser();
        $user     = $repository->findBy(['email' => $currUser->getUserIdentifier()])[0];

        $oldUserpic  = $user->getUserpic();
        $oldFilepath = $this->getParameter('upload_path') . '/' . $oldUserpic;


        $form   = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $errors = null;

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $userpicFile */
            $userpicFile = $form->get('userpic')->getData();
            if ($userpicFile) {
                $originalFilename = pathinfo($userpicFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$userpicFile->guessExtension();
                try {
                    $userpicFile->move(
                        $this->getParameter('upload_path'),
                        $newFilename
                    );
                    $user->setUserpic($newFilename);
                    if ($oldUserpic && file_exists($oldFilepath)) {
                        unlink($oldFilepath);
                    }
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    Printu::obj($e->getMessage())->title('[profile] FileException');
                }
            }

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
