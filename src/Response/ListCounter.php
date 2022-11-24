<?php
/**
 * Created by PhpStorm
 * Project: sys4
 * User:    mpak
 * Date:    28.08.2022
 * Time:    19:04
 */

namespace App\Response;

use Symfony\Component\HttpFoundation\Request;

class ListCounter
{
    /** @var int */
    public $allItems;
    /** @var int */
    public $pageItems;
    /** @var int */
    public $limit;
    /** @var int */
    public $offset;
    /** @var array */
    public $pages = [];
    /** @var int */
    public $currentPage = 1;
    /** @var array */
    public $previewPage;
    /** @var array */
    public $nextPage;

    /** @var array */
    private $queryParams;

    private function makeQueryString(array $fields)
    {
        $result = [];
        foreach ($fields as $key => $val) {
            $result[] = "{$key}={$fields[$key]}";
        }
        foreach ($this->queryParams as $key => $val) {
            if (array_key_exists($key, $fields)) {
                continue;
            }
            $result[] = "{$key}={$this->queryParams[$key]}";
        }
        return implode('&', $result);
    }

    public function setPages(Request $request)
    {
        $uri = str_replace($request->server->get('QUERY_STRING'), '', $request->server->get('REQUEST_URI'));
        if (strpos($uri,'?') === false) {
            $uri = $uri . '?';
        }
        $this->queryParams = $request->query->all();

        if (!$this->limit) {
            $this->pages = [];
            return;
        }

        $allPages          = ceil($this->allItems / $this->limit);
        $this->currentPage = ceil($this->offset / $this->limit) + 1;

        // Если больше 4-ех страниц
        if ($allPages > 4) {
            $initPageMax = 1;
            // рисуем одну.
            for ($i = 1; $i < $initPageMax + 1; $i++) {
                $offset = $this->limit * ($i - 1);
                $this->pages[] = [
                    'current' => ($i == $this->currentPage ? true : false),
                    'page'    => $i,
                    'url'     => ($this->currentPage != $i ? $uri . $this->makeQueryString(['limit' => $this->limit, 'offset' => $offset]) : null),
                ];
            }
            // если больше 5 страниц
            if ($allPages > 5)	{
                // Если это не первая и не последняя страница
                if ($this->currentPage > 1  && $this->currentPage < $allPages)	{
                    // если страница 4 и больше то делаем отрыв
                    if ($this->currentPage > 3) { //$page_string .= ( $on_page > 3 ) ? '<div class="page points">...</div>' : ' ';
                        $this->pages[] = [
                            'current' => false,
                            'page'    => '...',
                            'url'     => null,
                        ];
                    }
                    $initPageMin = ( $this->currentPage > 2 ) ? $this->currentPage : 3;
                    $initPageMin = ( $this->currentPage == $allPages - 1 ) ? $initPageMin - 1 : $initPageMin;

                    $initPageMax = ( $this->currentPage < $allPages - 3 ) ? $this->currentPage : $allPages - 3;
                    for ($i = $initPageMin - 1; $i < $initPageMax + 3; $i++) {
                        $offset = $this->limit * ($i - 1);
                        $this->pages[] = [
                            'current' => ($i == $this->currentPage ? true : false),
                            'page'    => $i,
                            'url'     => ($this->currentPage != $i ? $uri . $this->makeQueryString(['limit' => $this->limit, 'offset' => $offset]) : null),
                        ];
                    }
                    // если страница 4 и больше то делаем отрыв
                    if ($this->currentPage < $allPages - 3 ) {
                        $this->pages[] = [
                            'current' => false,
                            'page'    => '...',
                            'url'     => null,
                        ];
                    }
                } elseif ($this->currentPage == 1) { // Если же первая дорисуем до 4-ех
                    $initPageMax = 4;
                    for ($i = 2; $i < $initPageMax + 1; $i++) {
                        $offset = $this->limit * ($i - 1);
                        $this->pages[] = [
                            'current' => ($i == $this->currentPage ? true : false),
                            'page'    => $i,
                            'url'     => ($this->currentPage != $i ? $uri . $this->makeQueryString(['limit' => $this->limit, 'offset' => $offset]) : null),
                        ];
                    }
                    $this->pages[] = [
                        'current' => false,
                        'page'    => '...',
                        'url'     => null,
                    ];
                } else { // или последняя то блок многоточия после стартовой страницы
                    $this->pages[] = [
                        'current' => false,
                        'page'    => '...',
                        'url'     => null,
                    ];
                }
                // рисуем хвост из последней страницы
                if ($this->currentPage == $allPages)	{
                    $last = 3;
                } else {
                    $last = 0;
                }
                for ($i = ($allPages - $last); $i < $allPages + 1; $i++) {
                    $offset = $this->limit * ($i - 1);
                    $this->pages[] = [
                        'current' => ($i == $this->currentPage ? true : false),
                        'page'    => $i,
                        'url'     => ($this->currentPage != $i ? $uri . $this->makeQueryString(['limit' => $this->limit, 'offset' => $offset]) : null),
                    ];
                }
            } else {
                for ($i = 2; $i < $allPages + 1; $i++) {
                    $offset = $this->limit * ($i - 1);
                    $this->pages[] = [
                        'current' => ($i == $this->currentPage ? true : false),
                        'page'    => $i,
                        'url'     => ($this->currentPage != $i ? $uri . $this->makeQueryString(['limit' => $this->limit, 'offset' => $offset]) : null),
                    ];
                }
            }
        } else {
            for($i = 1; $i < $allPages + 1; $i++) {
                $offset = $this->limit * ($i - 1);
                $this->pages[] = [
                    'current' => ($i == $this->currentPage ? true : false),
                    'page'    => $i,
                    'url'     => ($this->currentPage != $i ? $uri . $this->makeQueryString(['limit' => $this->limit, 'offset' => $offset]) : null),
                ];
            }
        }

        if ($this->currentPage > 1) {
            $offset = $this->limit * ($this->currentPage - 2);
            $this->previewPage = [
                'page' => ($this->currentPage - 1),
                'url'  => $uri . $this->makeQueryString(['limit' => $this->limit, 'offset' => $offset]),
            ];
        }
        if ($this->currentPage < $allPages) {
            $offset = $this->limit * $this->currentPage;
            $this->nextPage = [
                'page' => ($this->currentPage + 1),
                'url'  => $uri . $this->makeQueryString(['limit' => $this->limit, 'offset' => $offset]),
            ];
        }
    }
}
