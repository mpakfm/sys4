<?php
/**
 * Created by PhpStorm
 * Project: sys4
 * User:    mpak
 * Date:    21.08.2022
 * Time:    1:07
 */

namespace App\Controller\Manage;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends BaseController
{
    /**
     * @Route("/manage", name="app_manage_index")
     */
    public function index(Request $request): Response
    {
        $this->preLoad($request);
        //$hasAccess = $this->isGranted('ROLE_ADMIN');
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->baseRender('manage/index/index.html.twig', [
            'controller_name' => 'IndexController',
            'menu' => [
                'pount' => 'dashboard'
            ]
        ]);
    }
}
