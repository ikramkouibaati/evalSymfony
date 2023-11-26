<?php

namespace App\Repository;

use App\Entity\Vehicule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vehicule>
 *
 * @method Vehicule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vehicule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vehicule[]    findAll()
 * @method Vehicule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VehiculeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicule::class);
    }

    public function add(Vehicule $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Vehicule $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllVehiculeFreeQuery($date_deb, $date_fin){
        $queryBuilder = $this->createQueryBuilder('v');

        $queryBuilder
            ->leftJoin('v.commandes', 'c')
            ->andWhere('c.id IS NULL OR (:dateDebut < c.date_heur_depart AND 
            :dateFin < c.date_heur_depart) 
            OR (:dateDebut > c.date_heur_fin AND 
            :dateFin > c.date_heur_fin)')
            ->setParameter('dateDebut', $date_deb)
            ->setParameter('dateFin', $date_fin);

        return $queryBuilder;
    }

    public function findAllVehiculeFree($date_deb, $date_fin){
        $queryBuilder = $this->findAllVehiculeFreeQuery($date_deb, $date_fin);

        return $queryBuilder->getQuery()->getResult();
    }

    public function findVehiculeByDatesAndFiltre($data, $date_deb, $date_fin){
        $queryBuilder = $this->findAllVehiculeFreeQuery($date_deb, $date_fin);
        foreach ($data as $key => $value){
            if(str_starts_with($key, 'order')){
                $key = str_replace('order', '', $key);
                $queryBuilder->orderBy('v.'.$key, $value);
            } else if($value != null){
                $queryBuilder->andWhere('upper(v.'.$key.') LIKE upper(:'.$key.')')
                    ->setParameter(':'.$key, '%' . $value . '%');
            }
        }
        return $queryBuilder->getQuery()->getResult();
    }

}