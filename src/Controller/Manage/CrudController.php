<?php
/**
 * Created by PhpStorm
 * Project: sys4
 * User:    mpak
 * Date:    31.10.2022
 * Time:    20:46
 */

namespace App\Controller\Manage;

use App\Response\ListCounter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Mpakfm\Printu;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CrudController extends AdminController
{
    /** @var int */
    public $defaultLimit = 20;
    /** @var int */
    public $defaultOffset = 0;
    /** @var string[] */
    public $defaultOrder = [
        'id' => 'desc'
    ];
    /** @var string[] */
    public $accessOrder = ['id'];
    /** @var string[] */
    public $accessFilter = ['id'];
    /** @var int */
    public $limit;
    /** @var int */
    public $offset;
    /** @var string[] */
    public $order;

    /** @var ServiceEntityRepositoryInterface */
    public $repository;
    /** @var ListCounter */
    public $counter;

    protected function prepareList(Request $request)
    {
        $this->counter = new ListCounter();

        $sort  = $request->get('sort');
        $order = $request->get('order');
        if ($sort && !empty($sort)) {
            $this->order[$sort] = ($order == 'desc' ? 'desc' : 'asc');
        }

        $this->limit  = $request->get('limit') ?? $this->defaultLimit;
        $this->offset = $request->get('offset') ?? $this->defaultOffset;
        $this->limit  = (int) $this->limit;
        $this->offset = (int) $this->offset;
        $this->order  = $this->order ?? $this->defaultOrder;

        Printu::info('===========================')->title('[prepareList]');
    }

    protected function setCounter(Request $request, array $search)
    {
        $this->counter->allItems  = $search['all'];
        $this->counter->pageItems = $search['on_page'];

        $this->counter->limit  = $this->limit;
        $this->counter->offset = $this->offset;

        $this->counter->setPages($request);
    }

    protected function setFilter(array $criteria): array
    {
        $filter = [];
        foreach ($this->accessFilter as $field) {
            $filter[$field] = (array_key_exists($field, $criteria) ? $criteria[$field] : '');
        }

        Printu::info($filter)->title('[CrudController::setFilter] $filter');
        return $filter;
    }

    public function delete(int $id): Response
    {
        if (!$id) {
            throw new NotFoundHttpException('Объект не найден');
        }
        $item = $this->repository->find($id);
        if (!$item) {
            throw new NotFoundHttpException('Объект не найден');
        }
        $this->repository->remove($item, true);
        return $this->json([
            'result' => true,
        ]);
    }

    public function deleteBulk(Request $request): Response
    {
        $ids = $request->request->get('ids');
        if (empty($ids)) {
            return $this->json([
                'count'  => 0,
            ]);
        }
        $this->repository->deleteBulk($ids);
        return $this->json([
            'count'  => count($ids),
        ]);
    }
}
