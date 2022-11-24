<?php

namespace App\Controller\Manage;

use App\Repository\StatClientConnectionsRepository;
use App\Response\ListCounter;
use App\Service\ContentManager;
use Mpakfm\Printu;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StatisticController extends CrudController
{
    /** @var string[] */
    public $accessOrder = ['id', 'client_id', 'user_id', 'date_time', 'remote_addr', 'user_agent', 'url', 'page', 'ping'];
    /** @var string[] */
    public $accessFilter = ['id', 'client_id', 'user_id', 'date_time', 'remote_addr', 'user_agent', 'url', 'page', 'ping'];

    public function __construct(StatClientConnectionsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request, ContentManager $contentManager): Response
    {
        $this->preLoad($request, $contentManager);

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
        $search   = $this->repository->searchByNames($query, $criteria, $this->order, $this->limit == 0 ? null : $this->limit, $this->offset);
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

}
