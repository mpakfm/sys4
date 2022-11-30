<?php

namespace App\Controller\Manage;

use App\Entity\ContentBlock;
use App\Form\ContentType;
use App\Repository\ContentBlockRepository;
use App\Response\ListCounter;
use App\Service\ContentManager;
use Mpakfm\Printu;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContentBlockController extends CrudController
{
    /** @var string[] */
    public $accessOrder = ['id', 'name', 'code', 'dt_create', 'dt_update', 'user_create', 'user_update', 'active', 'sort'];
    /** @var string[] */
    public $accessFilter = ['id', 'name', 'code', 'dt_create', 'dt_update', 'user_create', 'user_update', 'active', 'sort'];
    /** @var string[] */
    public $filterFields = [
        'id' => [
            'type' => 'int',
        ],
        'name' => [
            'type' => 'string',
        ],
        'code' => [
            'type' => 'string',
        ],
        'dt_create' => [
            'type' => 'datetime',
        ],
        'dt_update' => [
            'type' => 'datetime',
        ],
        'user_create' => [
            'type' => 'int',
        ],
        'user_update' => [
            'type' => 'int',
        ],
        'active' => [
            'type' => 'select',
            'values' => [
                '' => 'Не выбрано',
                '1' => 'Да',
                '0' => 'Нет',
            ]
        ],
        'sort' => [
            'type' => 'int',
        ],
    ];

    public function __construct(ContentBlockRepository $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }

    public function index(Request $request, ContentManager $contentManager): Response
    {
        $this->preLoad($request, $contentManager);
        $this->prepareList($request);

        $criteria = [];
        $filters = $request->query->all();
        foreach ($filters as $field => $value) {
            if (!in_array($field, $this->accessFilter) || $value == '') {
                continue;
            }
            if ($field == 'dt_create' || $field == 'dt_update') {
                Printu::info($value)->title('[ContentBlockController] dt_create/dt_update');
                $value = \DateTime::createFromFormat('d.m.Y', $value);
                $value->setTime(0, 0, 0);
            }
            $criteria[$field] = $value;
        }
        $query    = $request->get('query') ? $request->get('query') : '';
        $search   = $this->repository->searchByNames($query, $criteria, $this->order, $this->limit == 0 ? null : $this->limit, $this->offset);
        $elements = $search['list'];

        $this->setCounter($request, $search);
        $filter = $this->setFilter($criteria);

        $renderParams = [
            'query'    => $query,
            'filter'   => $filter,
            'fields'   => $this->filterFields,
            'elements' => $elements,
            'counter'  => $this->counter,
            'order'    => $request->get('order'),
            'sort'     => $request->get('sort'),
            'menu' => [
                'section' => 'content',
                'pount'   => 'block',
            ]
        ];
        Printu::info($renderParams)->title('[ContentBlockController::index] $renderParams');

        return $this->baseRender('manage/content/index.html.twig', $renderParams);
    }

    public function create(Request $request, ContentManager $contentManager)
    {
        $this->preLoad($request, $contentManager);

        $item = new ContentBlock();
        $form = $this->createForm(ContentType::class, $item);
        $form->handleRequest($request);

        $errors = null;

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ContentBlock $item */
            $item = $form->getData();

            try {
                $this->repository->add($item, true);
                return $this->redirectToRoute('app_manage_content_block');
            } catch (\Throwable $exception) {
                Printu::info($exception->getMessage())->title('Exception');
            }

        } elseif ($form->isSubmitted() && !$form->isValid()) {
            /** @var FormErrorIterator $errors */
            $errors = $form->getErrors(true, false);
        }

        return $this->baseRenderForm('manage/content/block/create.html.twig', [
            'menu' => [
                'pount' => 'users'
            ],
            'errors'  => $errors,
            'form'    => $form,
            'item'    => $item,
        ]);
    }

    public function edit(int $id,
        Request $request,
        ValidatorInterface $validator,
        ContentManager $contentManager): Response
    {
        $this->preLoad($request, $contentManager);
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException('Access Denied.');
        }
        if (!$id) {
            throw new NotFoundHttpException('Блок не найден');
        }
        $item = $this->repository->find($id);
        if (!$item) {
            throw new NotFoundHttpException('Блок не найден');
        }
        $form = $this->createForm(ContentType::class, $item);
        $form->handleRequest($request);
        $errors = null;
        if ($form->isSubmitted() && $form->isValid()) {

        }
        return $this->baseRenderForm('manage/content/block/edit.html.twig', [
            'menu' => [
                'pount' => 'users'
            ],
            'errors' => $errors,
            'item'   => $item,
            'form'   => $form,
        ]);
    }

    public function copy(int $id,
        Request $request,
        ValidatorInterface $validator,
        ContentManager $contentManager): Response
    {
        $this->preLoad($request, $contentManager);
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException('Access Denied.');
        }
        if (!$id) {
            throw new NotFoundHttpException('Блок не найден');
        }
        $item = $this->repository->find($id);
        if (!$item) {
            throw new NotFoundHttpException('Блок не найден');
        }
        $form = $this->createForm(ContentType::class, $item);
        $form->handleRequest($request);
        $errors = null;
        if ($form->isSubmitted() && $form->isValid()) {

        }
        return $this->baseRenderForm('manage/content/block/copy.html.twig', [
            'menu' => [
                'pount' => 'users'
            ],
            'errors' => $errors,
            'item'   => $item,
            'form'   => $form,
        ]);
    }
}
