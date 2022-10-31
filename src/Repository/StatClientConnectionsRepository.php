<?php

namespace App\Repository;

use App\Entity\StatClientConnections;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Mpakfm\Printu;

/**
 * @extends ServiceEntityRepository<StatClientConnections>
 *
 * @method StatClientConnections|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatClientConnections|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatClientConnections[]    findAll()
 * @method StatClientConnections[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatClientConnectionsRepository extends ServiceEntityRepository
{
    /** @var EntityManagerInterface */
    public $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct($registry, StatClientConnections::class);
    }

    public function add(StatClientConnections $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(StatClientConnections $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getPopularPages(int $limit = 5): ?array
    {
        $conn = $this->entityManager->getConnection();
        $sql  = "select s.page, count(s.id) as cnt
from stat_client_connections s
where s.ping = 0
group by s.page
order by cnt desc
limit {$limit}";
        $stmt = $conn->executeQuery($sql);
        Printu::info($stmt->rowCount())->title('[StatClientConnectionsRepository::getPopularPages] rowCount');
        if (!$stmt->rowCount()) {
            return null;
        }
        return $stmt->fetchAllAssociative();
    }

    public function getOnline(int $limit = 5): ?array
    {
        $conn = $this->entityManager->getConnection();
        $sql  = "select s.client_id, s.user_agent, count(s.id) as cnt, max(s.date_time) as dt, max(s.user_id) as user_id, u.email
from stat_client_connections s
left join user u on s.user_id = u.id
where s.date_time > SUBTIME(CURRENT_TIMESTAMP, '0 0:0:45')
group by s.client_id
order by  dt desc
limit {$limit}";
        $stmt = $conn->executeQuery($sql);
        Printu::info($stmt->rowCount())->title('[StatClientConnectionsRepository::getOnline] rowCount');
        if (!$stmt->rowCount()) {
            return null;
        }
        $items = [];
        while($row = $stmt->fetchAssociative()) {
            $row['dt'] = \DateTime::createFromFormat('Y-m-d H:i:s', $row['dt']);
            $items[] = $row;
        }
        return $items;
    }

    public function deleteHistory(int $days, bool $isOnlyPing = true)
    {
        if (!$days) {
            return;
        }
        $addWhere = '';
        if ($isOnlyPing) {
            $addWhere = " and ping = 1";
        }
        $sql = "delete from stat_client_connections where date_time < SUBTIME(CURRENT_TIMESTAMP, '{$days} 0:0:0') {$addWhere}";
        $conn = $this->entityManager->getConnection();
        $conn->executeQuery($sql);
    }

    public function getCount(array $criteria)
    {
        $conn = $this->getEntityManager()->getConnection();

        $where     = '';
        $whereLine = [];
        if (!empty($criteria)) {
            foreach ($criteria as $field => $value) {
                if (is_object($value) && class_implements('DateTime')) {
                    $value = $value->format('Y-m-d');
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
                $whereLine[] = "{$field} {$compare} {$value}";
            }
            if (!empty($whereLine)) {
                $where = "WHERE " . implode(' AND ', $whereLine);
            }
        }

        $sqlAll = "
            SELECT id FROM stat_client_connections
            {$where}
            ";
        Printu::info($sqlAll)->title('[getCount] $sqlAll');
        $stmtAll   = $conn->prepare($sqlAll);
        $resultAll = $stmtAll->executeQuery();
        $all       = $resultAll->rowCount();

        return $all;
    }

    public function searchByNames(string $query = '', array $criteria = null, array $orderBy = null, $limit = null, $offset = null)
    {
        Printu::info($criteria)->title('[searchByNames] $criteria');
        $conn = $this->getEntityManager()->getConnection();
        $where = '';
        $filter = ['1 = 1'];

        if (!empty($criteria)) {
            foreach ($criteria as $field => $value) {
                if (is_object($value) && class_implements('DateTime')) {
                    $value = $value->format('Y-m-d');
                    $filter[] = "DATE(s.{$field}) = '{$value}'";
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
                $filter[] = "s.{$field} {$compare} {$value}";
            }
        }
        $where = implode(' AND ', $filter);

        if ($query == '') {
            $sqlAll = "
            SELECT s.id FROM stat_client_connections s
            WHERE {$where}";
        } else {
            $sqlAll = "
            SELECT s.id FROM stat_client_connections s
            WHERE 
                (s.client_id LIKE ('%{$query}%') OR s.user_id LIKE ('%{$query}%') OR s.user_agent LIKE ('%{$query}%') OR s.page LIKE ('%{$query}%'))
                AND
                {$where}";
        }
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

        if ($query == '') {
            $sql = "
            SELECT s.* FROM stat_client_connections s
            WHERE {$where}
            {$order} {$counter}
            ";
        } else {
            $sql = "
            SELECT s.* FROM stat_client_connections s
            WHERE 
                (s.client_id LIKE ('%{$query}%') OR s.user_id LIKE ('%{$query}%') OR s.user_agent LIKE ('%{$query}%') OR s.page LIKE ('%{$query}%'))
                AND
                {$where}
            {$order} {$counter}
            ";
        }
        Printu::info($sql)->title('[searchByNames] $sql');
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $onPage    = $resultSet->rowCount();

        $result = [];
        while ($item = $resultSet->fetchAssociative()) {
            $connect = new StatClientConnections();
            $connect->setId($item['id']);
            $connect->setClientId($item['client_id']);
            $connect->setUserAgent($item['user_agent']);
            $connect->setRemoteAddr($item['remote_addr']);
            $connect->setDateTime(\DateTime::createFromFormat('Y-m-d H:i:s', $item['date_time']));
            $connect->setUrl($item['url']);
            $connect->setPage($item['page']);
            if ($item['user_id']) {
                $connect->setUserId($item['user_id']);
            }
            if ($item['ping']) {
                $connect->setPing($item['ping']);
            }
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
            DELETE FROM stat_client_connections
            WHERE id IN (" . implode(', ', $ids) . ")
            ";
        $stmtAll   = $conn->prepare($sqlAll);
        $stmtAll->executeQuery();
    }

//    /**
//     * @return StatClientConnections[] Returns an array of StatClientConnections objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?StatClientConnections
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
