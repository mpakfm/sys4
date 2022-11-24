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
use App\Service\ContentManager;
use Mpakfm\Printu;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AdminController extends BaseController
{
    public $contentBlockList;

    public function preLoad(Request $request, ?ContentManager $contentManager = null)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access Denied.');
        }
        if ($contentManager) {
            $this->contentBlockList = $contentManager->getContent(false);
            Printu::info($this->contentBlockList)->title('[AdminController] $this->contentBlockList');
        }
        parent::preLoad($request, $contentManager);
    }

    public function baseRender(string $view, array $parameters = [], Response $response = null, $last_modified = null): Response
    {
        $parameters['contentList'] = $this->contentBlockList;
        return parent::baseRender($view, $parameters, $response, $last_modified);
    }
}
