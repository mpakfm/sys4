<?php
/**
 * Created by PhpStorm
 * Project: newspaper
 * User:    mpak
 * Date:    16.08.2022
 * Time:    0:14
 */

namespace App\Controller\Manage;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IconsController extends AdminController
{
    /**
     * @Route("/manage/icons", name="app_manage_icons")
     */
    public function index(Request $request): Response
    {
        $this->preLoad($request);
        return $this->baseRender('manage/icons/index.html.twig', [
            'controller_name' => 'IndexController',
            'menu' => [
                'pount' => 'icons'
            ]
        ]);
    }
}
