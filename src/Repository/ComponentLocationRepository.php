<?php

declare(strict_types=1);

namespace Silverback\ApiComponentBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Silverback\ApiComponentBundle\Entity\Component\ComponentLocation;
use Silverback\ApiComponentBundle\Entity\Content\Page\Dynamic\AbstractDynamicPage;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ComponentLocation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ComponentLocation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ComponentLocation[]    findAll()
 * @method ComponentLocation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComponentLocationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ComponentLocation::class);
    }

    public function findByDynamicPage(AbstractDynamicPage $page): array
    {
        $qb = $this->createQueryBuilder('location');
        $qb
            ->andWhere(
                $qb->expr()->eq('location.dynamicPageClass', ':cls')
            )
            ->setParameter('cls', \get_class($page))
            ->addOrderBy('location.sort', 'ASC');
        return $qb->getQuery()->getResult();
    }
}
