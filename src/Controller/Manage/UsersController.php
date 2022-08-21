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
use Mpakfm\Printu;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
    public function create(Request $request, UserRepository $repository, ValidatorInterface $validator): Response
    {
        $this->preLoad($request);

//        $roles = $this->container->getParameter('security.role_hierarchy.roles');
//        Printu::obj($roles)->title('$roles');

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        Printu::obj(get_class($form))->title('get_class($form)');
        Printu::obj($_POST)->title('$_POST');
        Printu::obj($form->isSubmitted())->title('isSubmitted');
//        if (!empty($_POST)) {
//            $em2 = $this->container->get('doctrine');
//            Printu::obj(get_class($em2))->title('get_class($em2)');
//        }

        if ($form->isSubmitted()) {
            Printu::obj($form->isValid())->title('isValid');
        }

        $errors = null;

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            Printu::obj($user)->title('$form->getData() $user');
            $errors = $validator->validate($user);
            Printu::obj($errors)->title('validate $errors');
            if (!count($errors)) {
                $repository->add($user, true);
                return $this->redirectToRoute('app_manage_users');
            }
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            //$errors = $form->getErrors();
            /** @var FormErrorIterator $errors */
            $errors = $form->getErrors(true, false);
            if (is_object($errors)) {
                Printu::info(get_class($errors))->title('get_class($errors)');
                Printu::info($errors->count())->title('$errors->count()');
                /**
                 * @var int $key
                 * @var FormErrorIterator $err
                 */
                foreach ($errors as $key => $err) {
                    if (is_object($err)) {
                        Printu::info($err->count())->title($key . ' $err->count()');
                        Printu::info(get_class($err->current()))->title($key . ' $err->current()');
                        /** @var FormError $current */
                        $current = $err->current();
                        $children = $err->getChildren();
                        Printu::info($current->getMessage())->title($key . ' $current getMessage');
                        Printu::info(get_class($children))->title($key . ' get_class($children)');
//                        Printu::info($current->getCause())->title($key . ' $current getCause');
                        Printu::info($current->getMessageParameters())->title($key . ' $current getMessageParameters');
                        Printu::info($current->getMessagePluralization())->title($key . ' $current getMessagePluralization');
                    }
                }
            }
        }

        //Printu::obj(count($errors))->title('count($errors)');

//        return $this->render('manage/users/create.html.twig', [
//            'menu' => [
//                'pount' => 'users'
//            ],
//            'error'   => $error,
//            'form'    => $form->createView(),
//            'element' => $user
//        ]);

        return $this->baseRenderForm('manage/users/create.html.twig', [
            'menu' => [
                'pount' => 'users'
            ],
            'errors'  => $errors,
            'form'    => $form,
        ]);
    }

}
