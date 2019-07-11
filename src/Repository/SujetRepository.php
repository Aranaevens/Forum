<?php

namespace App\Repository;

use App\Entity\Sujet;
use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Sujet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sujet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sujet[]    findAll()
 * @method Sujet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SujetRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Sujet::class);
    }

    public function getAll()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
                            'SELECT s
                            FROM App\Entity\Sujet s');
        return $query->execute();
    }

    public function getAllFromCategorie($id_categorie)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
                                'SELECT s
                                FROM App\Entity\Sujet s
                                WHERE s.categorie = :categorie');
        $query->setParameter('categorie', $id_categorie);
        return $query->execute();
        // $query = $entityManager->createQueryBuilder()
        // ->select('s')
        // ->from('App\Entity\Sujet', 's')
        // ->innerJoin('App\Entity\Categorie', 'c')
        // ->where('c.id = :id')
        // ->setParameter('id', $id_categorie)
        // ->getQuery();
    }

    public function getAllFromUser($id_user)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
                                'SELECT s
                                FROM App\Entity\Sujet s
                                WHERE s.auteur = :user');
        $query->setParameter('user', $id_user);
        return $query->execute();
    }

    public function getAllFromSearch($pattern)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
                                'SELECT s
                                FROM App\Entity\Sujet s
                                WHERE s.titre LIKE :pat');
        $query->setParameter('pat', '%'.$pattern.'%');
        return $query->execute();
    }

    // /**
    //  * @return Sujet[] Returns an array of Sujet objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sujet
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
