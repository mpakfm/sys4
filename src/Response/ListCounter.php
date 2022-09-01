<?php
/**
 * Created by PhpStorm
 * Project: sys4
 * User:    mpak
 * Date:    28.08.2022
 * Time:    19:04
 */

namespace App\Response;

use Mpakfm\Printu;
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

        $this->queryParams = $request->query->all();

        if (!$this->limit) {
            $this->pages = [];
            return;
        }

        $allPages = ceil($this->allItems / $this->limit);

        $this->currentPage = ($this->offset / $this->limit) + 1;

        if ($allPages < 8) {
            for ($p = 1; $p <= $allPages; $p++) {
                $offset = $this->limit * ($p - 1);
                $this->pages[] = [
                    'current' => ($this->currentPage != $p ? false : true),
                    'page'    => $p,
                    'url'     => ($this->currentPage != $p ? $uri . $this->makeQueryString(['limit' => $this->limit, 'offset' => $offset]) : null),
                ];
            }
        } else {
            $startPages = 2;
            $endPages = $allPages - 2;
            $this->pages = [1, 2];
            if ($this->currentPage > $startPages && $this->currentPage < $endPages) {
                $this->pages[] = $this->currentPage - 1;
                $this->pages[] = $this->currentPage;
                $this->pages[] = $this->currentPage + 1;
            }
            $this->pages[] = $endPages - 1;
            $this->pages[] = $endPages;
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
