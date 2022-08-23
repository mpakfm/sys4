<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends BaseController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $this->preLoad($request);

        return $this->baseRender('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
}
