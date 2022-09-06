<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function add(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }

    public function getCount(array $criteria)
    {
        $conn = $this->getEntityManager()->getConnection();

        $where     = '';
        $whereLine = [];
        if (!empty($criteria)) {
            foreach ($criteria as $field => $value) {
                $whereLine[] = "{$field} = {$value}";
            }
            if (!empty($whereLine)) {
                $where = "WHERE " . implode(' AND ', $whereLine);
            }
        }

        $sqlAll = "
            SELECT id FROM user
            {$where}
            ";
        $stmtAll   = $conn->prepare($sqlAll);
        $resultAll = $stmtAll->executeQuery();
        $all       = $resultAll->rowCount();

        return $all;
    }

    public function deleteBulk(array $ids)
    {
        if (empty($ids)) {
            return;
        }
        $conn   = $this->getEntityManager()->getConnection();
        $sqlAll = "
            DELETE FROM user
            WHERE id IN (" . implode(', ', $ids) . ")
            ";
        $stmtAll   = $conn->prepare($sqlAll);
        $stmtAll->executeQuery();
    }

    public function searchByNames(string $query, array $orderBy = null, $limit = null, $offset = null)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sqlAll = "
            SELECT id FROM user u
            WHERE u.email LIKE ('%{$query}%') OR u.name LIKE ('%{$query}%') OR u.last_name LIKE ('%{$query}%')
            ";
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

        $sql = "
            SELECT * FROM user u
            WHERE u.email LIKE ('%{$query}%') OR u.name LIKE ('%{$query}%') OR u.last_name LIKE ('%{$query}%')
            {$order} {$counter}
            ";
        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $onPage    = $resultSet->rowCount();

        $result = [];
        while ($item = $resultSet->fetchAssociative()) {
            $user = new User();
            if ($item['userpic']) {
                $user->setUserpic($item['userpic']);
            }
            $user->setEmail($item['email']);
            $user->setIsVerified($item['is_verified']);
            if ($item['last_name']) {
                $user->setLastName($item['last_name']);
            }
            if ($item['name']) {
                $user->setName($item['name']);
            }
            $user->setRoles(json_decode($item['roles']));
            $result[] = $user;
        }
        return [
            'list'    => $result,
            'all'     => $all,
            'on_page' => $onPage,
        ];
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
