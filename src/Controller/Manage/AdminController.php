<?php
/**
 * Created by PhpStorm
 * Project: sys4
 * User:    mpak
 * Date:    26.10.2022
 * Time:    13:19
 */

namespace App\Controller\Manage;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AdminController extends BaseController
{
    public function preLoad(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access Denied.');
        }
        parent::preLoad($request);
    }
}
