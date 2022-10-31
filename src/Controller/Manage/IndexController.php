<?php
/**
 * Created by PhpStorm
 * Project: sys4
 * User:    mpak
 * Date:    21.08.2022
 * Time:    1:07
 */

namespace App\Controller\Manage;

use App\Repository\StatClientConnectionsRepository;
use Mpakfm\Printu;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AdminController
{
    /**
     * @Route("/manage", name="app_manage_index")
     */
    public function index(Request $request, StatClientConnectionsRepository $connectionsRepository): Response
    {
        $this->preLoad($request);
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');

        $online = $connectionsRepository->getOnline();
        $pages  = $connectionsRepository->getPopularPages();
        Printu::info($online)->title('$online');

        return $this->baseRender('manage/index/index.html.twig', [
            'controller_name' => 'IndexController',
            'menu' => [
                'pount' => 'dashboard'
            ],
            'online' => $online,
            'pages'  => $pages,
        ]);
    }
}
