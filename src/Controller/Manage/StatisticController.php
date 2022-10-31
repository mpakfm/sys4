<?php

namespace App\Controller\Manage;

use App\Repository\StatClientConnectionsRepository;
use App\Response\ListCounter;
use Mpakfm\Printu;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class StatisticController extends AdminController
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
    public $accessOrder = ['id', 'client_id', 'user_id', 'date_time', 'remote_addr', 'user_agent', 'url', 'page', 'ping'];
    /** @var string[] */
    public $accessFilter = ['id', 'client_id', 'user_id', 'date_time', 'remote_addr', 'user_agent', 'url', 'page', 'ping'];
    /** @var int */
    public $limit;
    /** @var int */
    public $offset;
    /** @var string[] */
    public $order;

    /**
     * @Route("/manage/statistic", name="app_manage_statistic")
     */
    public function index(Request $request, StatClientConnectionsRepository $repository): Response
    {
        $this->preLoad($request);

        $query   = null;
        $counter = new ListCounter();

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

        Printu::info('===========================')->title('[controller]');

        $criteria = [];
        $filters = $request->query->all();
        foreach ($filters as $field => $value) {
            if (!in_array($field, $this->accessFilter) || $value == '') {
                continue;
            }
            if ($field == 'date_time') {
                Printu::info($value)->title('[controller] date_time');
                $value = \DateTime::createFromFormat('d.m.Y', $value);
                $value->setTime(0, 0, 0);
            }
            $criteria[$field] = $value;
        }

        Printu::info($request->get('query'))->title('[controller] searchByNames query');
        Printu::info($criteria)->title('[controller] searchByNames $criteria');
        Printu::info($this->order)->title('[controller] searchByNames $this->order');
        $query    = $request->get('query') ? $request->get('query') : '';
        $search   = $repository->searchByNames($query, $criteria, $this->order, $this->limit == 0 ? null : $this->limit, $this->offset);
        $elements = $search['list'];

        $counter->allItems  = $search['all'];
        $counter->pageItems = $search['on_page'];

        $counter->limit  = $this->limit;
        $counter->offset = $this->offset;

        $counter->setPages($request);

        $filter = [];
        foreach ($this->accessFilter as $field) {
            $filter[$field] = (array_key_exists($field, $criteria) ? $criteria[$field] : '');
        }

        Printu::info($filter)->title('[controller] $filter');

        return $this->baseRender('manage/statistic/index.html.twig', [
            'query'    => $query,
            'filter'   => $filter,
            'elements' => $elements,
            'counter'  => $counter,
            'order'    => $order,
            'sort'     => $sort,
            'menu' => [
                'pount' => 'statistic'
            ]
        ]);
    }

    /**
     * @Route("/manage/statistic/delete/{id}", name="app_manage_statistic_delete")
     */
    public function delete(int $id, Request $request, StatClientConnectionsRepository $repository): Response
    {
        if (!$id) {
            throw new NotFoundHttpException('Объект не найден');
        }
        $actionUser = $this->getUser();
        $item       = $repository->find($id);
        if (!$item) {
            throw new NotFoundHttpException('Объект не найден');
        }
        $repository->remove($item, true);
        return $this->redirectToRoute('app_manage_statistic');
    }

    /**
     * @Route("/manage/statistic/delete_bulk", name="app_manage_statistic_delete_bulk", methods={"POST"})
     */
    public function deleteBulk(Request $request, StatClientConnectionsRepository $repository)
    {
        $ids = $request->request->get('ids');
        $repository->deleteBulk($ids);
        return $this->json([
            'count'  => count($ids),
        ]);
    }
}
