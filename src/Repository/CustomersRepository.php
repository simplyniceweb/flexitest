<?php

namespace App\Repository;

use App\Entity\Customers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Customers>
 *
 * @method Customers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customers[]    findAll()
 * @method Customers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customers::class);
    }

    public function add(Customers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Customers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
   /**
    * @return Customers[] Returns an array of Customers objects
    */
    public function findCustomers(): array
    {
        return $this->createQueryBuilder('c')
            ->select("CONCAT(c.first_name, ' ', c.last_name) AS full_name, c.email, c.country")
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult()
        ;
    }

   public function findOneBySomeField($value): array
   {
       return $this->createQueryBuilder("c")
            ->select("CONCAT(c.first_name, ' ', c.last_name) AS full_name, c.email, c.username, c.gender, c.country, c.city, c.phone")
           ->andWhere('c.id = :val')
           ->setParameter('val', (int) $value)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }

   //    /**
   //     * @return Customers[] Returns an array of Customers objects
   //     */
   //    public function findByExampleField($value): array
   //    {
   //        return $this->createQueryBuilder('c')
   //            ->andWhere('c.exampleField = :val')
   //            ->setParameter('val', $value)
   //            ->orderBy('c.id', 'ASC')
   //            ->setMaxResults(10)
   //            ->getQuery()
   //            ->getResult()
   //        ;
   //    }
}
