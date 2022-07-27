<?php

namespace App\Repository;

use App\Entity\Code;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Code>
 *
 * @method Code|null find($id, $lockMode = null, $lockVersion = null)
 * @method Code|null findOneBy(array $criteria, array $orderBy = null)
 * @method Code[]    findAll()
 * @method Code[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Code::class);
    }

    public function add(Code $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Code $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getCodeClasses(): array
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('c')
            ->where($qb->expr()->notLike('c.kod','\'%.%\''))
            ->andWhere($qb->expr()->neq('c.kod','\'\''))
            ->orderBy('CAST(c.kod AS INT)', 'ASC');
        $query = $qb->getQuery();

        return $query->getArrayResult();
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws Exception
     */
    public function getChildren($pattern): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT * FROM code WHERE kod ~ $pattern ORDER BY kod";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        return $resultSet->fetchAllAssociative();
    }

    public function truncateCodes()
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();
        $platform   = $connection->getDatabasePlatform();

        $connection->executeUpdate($platform->getTruncateTableSQL('code', true));
    }
}
