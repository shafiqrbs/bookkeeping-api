<?php

namespace App\Repository;

use App\Entity\AccountHead;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<AccountHead>
 *
 * @method AccountHead|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccountHead|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccountHead[]    findAll()
 * @method AccountHead[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountHeadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccountHead::class);
    }

    public function add(AccountHead $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AccountHead $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function getHeads($parameters)
    {
        $perPage = isset($parameters['per_page']) ? (int)$parameters['per_page'] : null;
        $page = isset($parameters['page']) ? (int)$parameters['page'] : null;
        $type = isset($parameters['type']) ? strtoupper(trim($parameters['type'])) : null;

        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.parent', 'parent');

        $qb->select('e.id', 'e.name', 'e.code', 'e.type', 'e.status', 'e.slug', 'e.createdBy');
        $qb->addSelect('parent.id AS parentId', 'parent.name AS parentName', 'parent.slug AS parentSlug');

        if ($type){
            $qb->andWhere($qb->expr()->eq('e.type', ':type'))->setParameter('type', $type);
            $qb->andWhere($qb->expr()->isNull('e.parent'));
        }
        if ($perPage && $page){
            $prevPage = $page > 0 ?  ($page - 1) : 0;
            $offset = $perPage * $prevPage;
            $qb->setFirstResult($offset);
            $qb->setMaxResults($perPage);
        }

        return $qb->getQuery()->getArrayResult();
    }

//    /**
//     * @return AccountHead[] Returns an array of AccountHead objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AccountHead
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
