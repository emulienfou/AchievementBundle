<?php

namespace Avoo\AchievementBundle\Repository;

use Avoo\AchievementBundle\Model\UserAchievementInterface;
use Avoo\AchievementBundle\Model\UserInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Class UserAchievementRepository
 *
 * @author Jérémy Jégou <jejeavo@gmail.com>
 */
class UserAchievementRepository extends EntityRepository
{
    /**
     * Get achievement
     *
     * @param string             $id
     * @param UserInterface|null $user
     *
     * @return UserAchievementInterface|null
     * @throws NonUniqueResultException
     */
    public function getAchievement($id, $user = null)
    {
        if (is_null($user)) {
            return null;
        }

        $achievement = $this->createQueryBuilder('a')
            ->where('a.achievement = :achievement')
            ->andWhere('a.user = :user')
            ->setParameter('achievement', $id)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();

        return $achievement;
    }

    /**
     * Find achievements by id's
     *
     * @param UserInterface $user
     * @param array         $ids
     * @param boolean|null  $isComplete
     *
     * @return UserAchievementInterface[]
     */
    public function findAchievements(UserInterface $user, array $ids, $isComplete = null)
    {
        $query = $this->createQueryBuilder('a')
            ->andWhere('a.user = :user')
            ->andWhere('a.achievement IN (:ids)')
            ->orderBy('a.completeAt', 'DESC')
            ->setParameter('ids', $ids)
            ->setParameter('user', $user);

        if (true === $isComplete) {
            $query->andWhere('a.completeAt IS NOT NULL');
        } else if (false === $isComplete) {
            $query->andWhere('a.completeAt IS NULL');
        }

        $achievements = $query->getQuery()
            ->getResult();

        return $achievements;
    }

    /**
     * Get latest achievements unlocked
     *
     * @param UserInterface $user
     * @param integer       $limit
     *
     * @return UserAchievementInterface[]
     * @throws NonUniqueResultException
     */
    public function getLatestAchievement(UserInterface $user, $limit = 1)
    {
        $achievements = $this->createQueryBuilder('a')
            ->andWhere('a.user = :user')
            ->andWhere('a.completeAt IS NOT NULL')
            ->orderBy('a.completeAt', 'DESC')
            ->setParameter('user', $user)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return $achievements;
    }
}
