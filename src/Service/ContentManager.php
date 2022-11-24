<?php
/**
 * Created by PhpStorm
 * Project: sys4
 * User:    mpak
 * Date:    02.11.2022
 * Time:    3:58
 */

namespace App\Service;

use App\Repository\ContentBlockRepository;

class ContentManager
{
    /** @var ContentBlockRepository */
    private $blockRepository;

    public function __construct(ContentBlockRepository $blockRepository)
    {
        $this->blockRepository = $blockRepository;
    }

    public function getContent(bool $isActive = true)
    {
        $query = $this->blockRepository
            ->createQueryBuilder('b');
        if ($isActive) {
            $query
                ->andWhere('b.active = :val')
                ->setParameter('val', 1);
        }
        return $query
            ->orderBy('b.sort', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
}
