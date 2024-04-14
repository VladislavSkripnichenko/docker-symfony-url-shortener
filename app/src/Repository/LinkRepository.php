<?php

namespace App\Repository;

use App\Entity\Link;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Link>
 *
 * @method Link|null find($id, $lockMode = null, $lockVersion = null)
 * @method Link|null findOneBy(array $criteria, array $orderBy = null)
 * @method Link[]    findAll()
 * @method Link[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Link::class);
    }

    /**
     * Finds a link by its short URL if it is still valid (not expired).
     *
     * @param string $shortUrl The short URL to search for.
     * @return Link|null The found Link or null if no valid link was found.
     */
    public function findLinkByShortUrl(string $shortUrl): ?Link
    {
        return $this->createQueryBuilder('l')
            ->where('l.shortUrl = :shortUrl')
            ->andWhere('l.expirationDate > :currentDate OR l.expirationDate IS NULL')
            ->setParameter('shortUrl', $shortUrl)
            ->setParameter('currentDate', new \DateTime(), \Doctrine\DBAL\Types\Types::DATETIME_MUTABLE)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Saves a Link entity.
     *
     * @param Link $link The Link entity to save.
     */
    public function save(Link $link)
    {
        $this->getEntityManager()->persist($link);
        $this->getEntityManager()->flush();
    }
}
