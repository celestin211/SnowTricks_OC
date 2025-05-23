<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * UtilisateurRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UtilisateurRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    const CASE_STATUT = "(CASE
                WHEN utilisateur.enabled=1 AND utilisateur.locked=0 THEN 'Actif'
                WHEN utilisateur.enabled=1 AND utilisateur.locked=1 THEN 'Bloqué'
                WHEN utilisateur.enabled=0 THEN 'Inactif'
                ELSE '' END)";

    const CASE_ROLE = "(CASE
                WHEN utilisateur.roles like '%\"ROLE_ELEVE\"%' THEN 'Administrateur'
                WHEN utilisateur.roles like '%\"ROLE_PROFESSEUR\"%' THEN 'PROFESSEUR VIP'
                ELSE '' END)";

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    // Retourne tous les utilisateurs Ministere actifs
    public function findUtilisateurs(array $roles = [], ?RendezVous $rendezVouses = null)
    {
        $qb = $this->createQueryBuilder('u');

        $qb->where('u.enabled = 1');        	// utilisateur actif

        if (!empty($roles)) {
            $where = '';
            foreach ($roles as $i => $role) {
                if ($i > 0) {
                    $where .= ' OR ';
                }

                $where .= 'u.roles LIKE :'.$role;
                $qb->setParameter($role, '%"'.$role.'"%');
            }
            $qb->andWhere($where);
        }

        if ($rendezVouses) {
            $qb->andWhere('u.rendezVouses = :rendezVouses')
                ->setParameter('rendezVouses', $rendezVouses);
        }

        return $qb->getQuery()->getResult();
    }

    public function findValideursByRendezVous($rendezVouses)
    {
        return $this->createQueryBuilder('u')
            ->where('u.rendezVouses = :rendezVouses')
            ->andWhere('u.roles LIKE :role')
            ->andWhere('u.enabled = 1')        	// utilisateur actif
            ->andWhere('u.locked = 0')			// utilisateur
            ->andWhere('u.expired = 0')			// utilisateur non expiré
            ->setParameter('rendezVouses', $rendezVouses)
            ->setParameter('role', '%"ROLE_PROFESSEUR"%')
            ->getQuery()
            ->getResult();
    }

    public function findUtilisateursByRendezVous($rendezVouses)
    {
        return $this->createQueryBuilder('u')
            ->where('u.rendezVouses = :rendezVouses')
            ->andWhere('u.enabled = 1')        	// utilisateur actif
            ->andWhere('u.locked = 0')			// utilisateur
            ->andWhere('u.expired = 0')			// utilisateur non expiré
            ->setParameter('rendezVouses', $rendezVouses)
            ->getQuery()
            ->getResult();
    }

    // Retourne tous les utilisateurs Ministere actifs
    public function findUtilisateursMinistere()
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :ROLE_ELEVE OR u.roles LIKE :ROLE_PROFESSEUR')
            ->andWhere('u.enabled = 1')			// utilisateur non expiré
            ->setParameter('ROLE_ELEVE', '%"ROLE_ELEVE"%')
            ->setParameter('ROLE_PROFESSEUR', '%"ROLE_PROFESSEUR"%')
            ->getQuery()
            ->getResult();
    }

    // Retourne tous les utilisateurs 
    public function findUtilisateursPorfs()
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :ROLE_PROFESSEUR')
            ->andWhere('u.enabled = 1')			// utilisateur non expiré
            ->setParameter('ROLE_PROFESSEUR', '%"ROLE_PROFESSEUR"%')
            ->getQuery()
            ->getResult();
    }

    // Retourner les utilisateurs non supprimés qui n'ont pas le rôle admin
    public function getUsersSaufAdmin()
    {
        $qb = $this->createQueryBuilder('u')
            ->leftJoin('u.rendezVouses', 'r')
            ->select('u.civilite')
            ->addSelect('u.nom')
            ->addSelect('u.prenom')
            ->addSelect('u.email')
            ->addSelect('u.roles')
            ->addSelect('u.enabled')
            ->addSelect('r.rendezVouses')
            ->where('u.roles not LIKE :professeur')
            ->andWhere('u.enabled = 1')			// utilisateur actif
            ->setParameter('professeur', '%"ROLE_PROFESSEUR"%')
            ->orderBy('r.rendezVouses')
            ->addOrderBy('u.nom')
            ->addOrderBy('u.prenom');

        return $qb->getQuery()->getResult();
    }

    // Retourner les utilisateurs non supprimés qui n'ont pas le rôle admin
    public function getUsers(bool $isAdmin = false)
    {
        $qb = $this->createQueryBuilder('u')
            ->leftJoin('u.rendezVouses', 'r')
            ->select('u.civilite')
            ->addSelect('u.nom')
            ->addSelect('u.prenom')
            ->addSelect('u.email')
            ->addSelect('u.roles')
            ->addSelect('u.enabled')
            ->addSelect('r.rendezVouses ');
        if (!$isAdmin) {
            $qb = $qb->andwhere('u.roles LIKE :ROLE_PROFESSEUR OR u.roles LIKE :ROLE_PROFESSEUR')
                ->andWhere('u.enabled = 1')			// utilisateur actif
                ->setParameter('ROLE_PROFESSEUR', '%"ROLE_PROFESSEUR"%')
                ->setParameter('ROLE_PROFESSEUR', '%"ROLE_PROFESSEUR"%');
        }

        $qb->andWhere('u.enabled = 1')			// utilisateur actif
        ->orderBy('r.rendezVouses')
            ->addOrderBy('u.nom')
            ->addOrderBy('u.prenom');

        return $qb->getQuery()->getResult();
    }


    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newEncodedPassword): void
    {
        // set the new encoded password on the User object
        $user->setPassword($newEncodedPassword);

        // execute the queries on the database
        $this->getEntityManager()->flush($user);
    }

    public function search(string $search, int $start, int $length, array $order, bool $isAdmin = false)
    {
        $colonnes = ['utilisateur.civilite', 'utilisateur.nom', 'utilisateur.prenom', 'utilisateur.email', 'utilisateur.rendezVouses', 'role', 'statut'];

        $qb = $this->createQueryBuilder('utilisateur')
            ->leftJoin('utilisateur.rendezVouses', 'rendezVouses')
            ->addSelect(self::CASE_STATUT.' AS statut')
            ->addSelect(self::CASE_ROLE.' AS role')
            ->addSelect(self::CASE_STATUT.' AS HIDDEN statut_h')

            ->orderBy($colonnes[$order[0]['column']], $order[0]['dir'])
        ;

        $qb = $this->addSearchWhere($qb, $search, $isAdmin);

        $query = $qb->getQuery()->setMaxResults($length)->setFirstResult($start);



        return $query->getResult();
    }

    public function searchCount(bool $isAdmin = false, string $search = null)
    {
        $qb = $this->createQueryBuilder('utilisateur')
            ->leftJoin('utilisateur.rendezVouses', 'rendezVouses')
            ->select('COUNT(utilisateur)')
        ;

        $qb = $this->addSearchWhere($qb, $search, $isAdmin);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function addSearchWhere(QueryBuilder $qb, ?string $search, bool $isAdmin)
    {
        $colonnesTexte = ['utilisateur.civilite', 'utilisateur.nom', 'utilisateur.prenom', 'utilisateur.email', 'rendezvous.rendezVouses'];

        if (strlen($search) > 0) {
            $orXSearch = $qb->expr()->orX();
            $conditions = [];

            foreach ($colonnesTexte as $colonne) {
                $conditions[] = $qb->expr()->like($colonne, ':SEARCH');
                $qb->setParameter('SEARCH', '%'.$search.'%');
            }

            $orXSearch->addMultiple($conditions);
            $qb->andWhere($orXSearch);
        }

        if (!$isAdmin) {
            $qb = $qb
                ->andwhere('utilisateur.roles LIKE :ROLE_ADMIN OR utilisateur.roles LIKE :ROLE_PROFESSEUR')
                ->andWhere('utilisateur.enabled = 1')			// utilisateur actif
                ->setParameter('ROLE_ADMIN', '%"ROLE_ADMIN"%')
                ->setParameter('ROLE_PROFESSEUR', '%"ROLE_PROFESSEUR"%');
        }

        return $qb;
    }
}
