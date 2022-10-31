<?php

namespace App\Controller;

use App\Repository\PropertiesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SpecialPageController extends BaseController
{
    /**
     * @Route("/special", name="app_special_page")
     */
    public function index(Request $request, PropertiesRepository $propsRepository): Response
    {
        $this->preLoad($request);

        $props = $propsRepository->findAll();
        $meta = [];
        if (!empty($props)) {
            $meta['title']       = $props[0]->getMetaTitle() ?? $props[0]->getMetaTitle();
            $meta['description'] = $props[0]->getMetaTitle() ?? $props[0]->getMetaDescription();
            $meta['keywords']    = $props[0]->getMetaTitle() ?? $props[0]->getMetaKeywords();
        }

        return $this->baseRender('special_page/index.html.twig', [
            'meta' => $meta,
            'controller_name' => 'SpecialPageController',
        ]);
    }
}
