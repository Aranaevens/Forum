<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\Sujet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function getAll()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
                            'SELECT m
                            FROM App\Entity\Message m');
        return $query->execute();
    }

    public function getAllFromSujet($id_sujet)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
                                'SELECT m
                                FROM App\Entity\Message m
                                WHERE m.sujet = :sujet');
        $query->setParameter('sujet', $id_sujet);
        return $query->execute();
    }

    public function getAllFromUser($id_user)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
                                'SELECT m
                                FROM App\Entity\Message m
                                WHERE m.auteur = :user');
        $query->setParameter('user', $id_user);
        return $query->execute();
    }

    public function getAllFromSearch($pattern)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
                                'SELECT m
                                FROM App\Entity\Message m
                                WHERE m.body LIKE :pat');
        
        $query->setParameter('pat', '%'.$pattern.'%');
        return $query->execute();
    }

    // /**
    //  * @return Message[] Returns an array of Message objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Message
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
