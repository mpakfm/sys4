<?php
/**
 * Created by PhpStorm
 * Project: sys4
 * User:    mpak
 * Date:    20.08.2022
 * Time:    23:53
 */

namespace App\Controller;

use App\Service\ContentManager;
use Mpakfm\Printu;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends AbstractController
{
    /**
     * @var bool
     */
    public $isCached  = true;

    /**
     * @var int
     */
    public $cacheTime = 3600;

    /**
     * @var string
     */
    public $canonical;

    protected $cookie;

    public function __construct()
    {
    }

    public function preLoad(Request $request, ?ContentManager $contentManager = null)
    {
        // cookie
        $cookie = $request->cookies->get('client');
        if (!$cookie) {
            $cookie = 'CI-' . time() . '-' . md5($request->headers->get('User-Agent'));
            $this->cookie = new Cookie(
                'client', $cookie, strtotime('tomorrow'), '/',
                $request->server->get('SERVER_NAME'), false, true
            );
        } else {
            $this->cookie = new Cookie(
                'client', $cookie, strtotime('tomorrow'), '/',
                $request->server->get('SERVER_NAME'), false, true
            );
        }
        $this->canonical =
            ($request->server->get('REQUEST_SCHEME') == 'https' ||
            $request->server->get('SERVER_PORT') != 80 ? 'https' : 'http') .
                '://' . $request->server->get('SERVER_NAME') . $request->server->get('REQUEST_URI');
    }

    public function baseRenderForm(string $view, array $parameters = [], Response $response = null, $last_modified = null): Response
    {
        if (null === $response) {
            $response = new Response();
        }

        foreach ($parameters as $k => $v) {
            if ($v instanceof FormView) {
                throw new \LogicException(sprintf('Passing a FormView to "%s::renderForm()" is not supported, pass directly the form instead for parameter "%s".', get_debug_type($this), $k));
            }

            if (!$v instanceof FormInterface) {
                continue;
            }

            $parameters[$k] = $v->createView();

            if (200 === $response->getStatusCode() && $v->isSubmitted() && !$v->isValid()) {
                $response->setStatusCode(422);
            }
        }
        return $this->baseRender($view, $parameters, $response, $last_modified);
    }

    public function baseRender(string $view, array $parameters = [], Response $response = null, $last_modified = null): Response
    {
        $parameters['canonical'] = $this->canonical;
        $parameters['user']      = $this->getUser();
        if (isset($parameters['meta'])) {
            if (!isset($parameters['meta']['title'])) {
                $parameters['meta']['title'] = 'Sys4';
            }
            if (!isset($parameters['meta']['description'])) {
                $parameters['meta']['description'] = 'Sys4 - 4 попытка';
            }
            if (!isset($parameters['meta']['keywords'])) {
                $parameters['meta']['keywords'] = 'Sys4';
            }
        } else {
            $parameters['meta'] = [
                'title'       => 'Sys4',
                'description' => 'Sys4 - 4 попытка',
                'keywords'    => 'Sys4',
            ];
        }
        if (null === $response) {
            $response = new Response();
        }
        if ($this->cookie) {
            $response->headers->setCookie($this->cookie);
        }
        if ($this->isCached && $this->cacheTime) {
            $response->setSharedMaxAge($this->cacheTime);
        }
        // (необязательно) установите пользовательскую директиву
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $dt      = new \DateTimeImmutable();
        $dtMonth = $dt->add(new \DateInterval('P1M'));
        $response->setExpires($dtMonth);
        // устанавливает заголовки для кэширования одним вызовом
        $response->setCache([
            'last_modified' => $last_modified ? $last_modified : new \DateTimeImmutable(),
            'max_age'       => $this->cacheTime ? $this->cacheTime : 0,
            's_maxage'      => $this->cacheTime ? $this->cacheTime : 0,
            'private'       => true,
        ]);

        return $this->render($view, $parameters, $response);
    }
}
