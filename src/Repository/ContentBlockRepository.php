<?php

namespace App\Repository;

use App\Entity\ContentBlock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Mpakfm\Printu;

/**
 * @extends ServiceEntityRepository<ContentBlock>
 *
 * @method ContentBlock|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContentBlock|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContentBlock[]    findAll()
 * @method ContentBlock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContentBlockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContentBlock::class);
    }

    public function add(ContentBlock $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ContentBlock $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function searchByNames(string $query = '', array $criteria = null, array $orderBy = null, $limit = null, $offset = null)
    {
        Printu::info($criteria)->title('[ContentBlockRepository::searchByNames] $criteria');
        $conn   = $this->getEntityManager()->getConnection();
        $filter = [];
        $where  = '';

        if (!empty($criteria)) {
            foreach ($criteria as $field => $value) {
                if (is_object($value) && class_implements('DateTime')) {
                    $value = $value->format('Y-m-d');
                    $filter[] = "DATE(el.{$field}) = '{$value}'";
                    continue;
                }
                $compare = '=';
                if (strpos($value, '!' === 0)) {
                    $compare = '!=';
                }
                if ($value != 'null') {
                    $value = "'{$value}'";
                } else {
                    if (strpos($value, '!' === 0)) {
                        $compare = 'is not';
                    } else {
                        $compare = 'is';
                    }
                }
                $filter[] = "el.{$field} {$compare} {$value}";
            }
        }
        if ($query !== '') {
            $filter[] = "(el.name LIKE ('%{$query}%')";
        }
        if (!empty($filter)) {
            $where = 'WHERE ' . implode(' AND ', $filter);
        }
        $sqlAll = "SELECT el.id FROM content_block el {$where}";

        Printu::info($sqlAll)->title('[ContentBlockRepository::searchByNames] $sqlAll');
        $stmtAll   = $conn->prepare($sqlAll);
        $resultAll = $stmtAll->executeQuery();
        $all       = $resultAll->rowCount();

        $order = '';
        if (!empty($orderBy)) {
            $orderList = [];
            foreach ($orderBy as $field => $val) {
                $orderList[] = "{$field} {$val}";
            }
            $order = 'ORDER BY ' . implode(', ', $orderList);
        }
        $counter = '';
        if (!is_null($limit)) {
            $counter = "LIMIT {$limit}";
            if (!is_null($offset)) {
                $counter = "LIMIT {$offset}, {$limit}";
            }
        }

        $sql = "SELECT el.* FROM content_block el {$where} {$order} {$counter}";
        Printu::info($sql)->title('[ContentBlockRepository::searchByNames] $sql');
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $onPage    = $resultSet->rowCount();

        $result = [];
        while ($item = $resultSet->fetchAssociative()) {
            $connect = new ContentBlock();
            $connect->setId($item['id']);
            $connect->setName($item['name']);
            $connect->setCode($item['code']);
            $connect->setDtCreate(\DateTime::createFromFormat('Y-m-d H:i:s', $item['dt_create']));
            $connect->setDtUpdate(\DateTime::createFromFormat('Y-m-d H:i:s', $item['dt_update']));
            $connect->setUserCreate($item['user_create']);
            $connect->setUserUpdate($item['user_update']);
            $connect->setActive($item['active']);
            $connect->setSort($item['sort']);

            $result[] = $connect;
        }
        return [
            'list'    => $result,
            'all'     => $all,
            'on_page' => $onPage,
        ];
    }

    public function deleteBulk(array $ids)
    {
        if (empty($ids)) {
            return;
        }
        $conn   = $this->getEntityManager()->getConnection();
        $sqlAll = "
            DELETE FROM content_block
            WHERE id IN (" . implode(', ', $ids) . ")
            ";
        $stmtAll   = $conn->prepare($sqlAll);
        $stmtAll->executeQuery();
    }
}
