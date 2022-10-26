<?php

namespace App\Controller;

use App\Repository\PropertiesRepository;
use Mpakfm\Printu;
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
    public function index(Request $request, PropertiesRepository $repository): Response
    {
        $this->preLoad($request);

        $props = $repository->findAll();
        Printu::info($props)->title('$props');
        $meta = [];
        if (!empty($props)) {
            $meta['title']       = $props[0]->getMetaTitle() ?? $props[0]->getMetaTitle();
            $meta['description'] = $props[0]->getMetaTitle() ?? $props[0]->getMetaDescription();
            $meta['keywords']    = $props[0]->getMetaTitle() ?? $props[0]->getMetaKeywords();
        }

        Printu::info($meta)->title('$meta');

        return $this->baseRender('index/index.html.twig', [
            'meta' => $meta,
            'controller_name' => 'IndexController',
        ]);
    }
}
