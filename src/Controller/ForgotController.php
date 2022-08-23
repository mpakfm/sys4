<?php

namespace App\Controller;

use App\Form\ForgotType;
use App\Repository\UserRepository;
use Mpakfm\Printu;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ForgotController extends BaseController
{
    /**
     * @Route("/forgot", name="app_forgot")
     */
    public function index(Request $request, UserRepository $repository, UserPasswordHasherInterface $userPasswordHasher, MailerInterface $mailer, RequestStack $requestStack): Response
    {
        $this->preLoad($request);

        $form   = $this->createForm(ForgotType::class);
        $form->handleRequest($request);
        $errors = null;

        $session = $requestStack->getSession();
        $isSent  = $session->get('isSent');

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $users = $repository->findBy(['email' => $email]);
            if (empty($users)) {
                $form->addError(new FormError('Нет такого пользователя'));
            } else {
                $user = $users[0];
                $random = random_int(1, 1000000);
                $newPass = substr(hash('sha1', $random), 0, 10);
                $user->setPassword(
                    $userPasswordHasher->hashPassword($user, $newPass)
                );

                $repository->add($user, true);

                $email = (new Email())
                    ->from('noreply@sys4.site')
                    ->to($user->getEmail())
                    ->subject('[Sys4.site] Восстановление пароля')
                    ->text('Ваш новый пароль: ' . $newPass)
                    ->html('<p>Ваш новый пароль: ' . $newPass . '</p>');

                if ($_ENV['APP_ENV'] == 'dev') {
                    Printu::info($newPass)->title('$newPass for ' . $user->getEmail());
                }

                $mailer->send($email);
                $session->set('isSent', true);
                return $this->redirectToRoute('app_forgot');
            }
        }

        if ($isSent) {
            $session->remove('isSent');
        }

        return $this->baseRenderForm('forgot/index.html.twig', [
            'form'   => $form,
            'error'  => $errors,
            'isSent' => $isSent,
        ]);
    }
}
