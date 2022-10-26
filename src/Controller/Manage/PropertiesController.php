<?php

namespace App\Controller\Manage;

use App\Form\PropertiesType;
use App\Form\UserType;
use App\Repository\PropertiesRepository;
use Mpakfm\Printu;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PropertiesController extends AdminController
{
    /**
     * @Route("/manage/properties", name="app_manage_properties")
     */
    public function index(Request $request, PropertiesRepository $repository): Response
    {
        $this->preLoad($request);

        $prop = $repository->findOneBy([]);

        Printu::info($prop)->title('[PropertiesController] $prop');

        $form   = $this->createForm(PropertiesType::class, $prop);
        $form->handleRequest($request);
        $errors = null;

        if ($form->isSubmitted() && $form->isValid()) {
            Printu::info($form->getData())->title('getData');
            Printu::info($prop)->title('$prop');
            $repository->add($prop, true);
            return $this->redirectToRoute('app_manage_properties');
        }

        return $this->baseRenderForm('manage/properties/index.html.twig', [
            'menu' => [
                'pount' => 'properties'
            ],
            'errors'  => $errors,
            'item'    => $prop,
            'form'    => $form,
        ]);
    }
}
