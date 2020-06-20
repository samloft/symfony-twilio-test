<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    /**
     * MessageRepository constructor.
     *
     * @param \Doctrine\Persistence\ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @return int|mixed|string
     */
    public function show()
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $id
     *
     * @return int|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function byId($id)
    {
        return $this->createQueryBuilder('m')
            ->where('m.id = '. $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
