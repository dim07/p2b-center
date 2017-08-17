<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * This custom Doctrine repository is empty because so far we don't need any custom
 * method to query for application user information. But it's always a good practice
 * to define a custom repository that will be used when the application grows.
 *
 * See http://symfony.com/doc/current/book/doctrine.html#custom-repository-classes
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class UserRepository extends EntityRepository
{
    public function projectsFromPeriodWithPays($userId, $d1, $d2, $legId = 0)
    {
        if ($legId == 0) {
            $results = $this->getEntityManager()
            ->createQuery(
                'SELECT DISTINCT j.id as id, c.name as leg, j.name as project, u.fio as gip, n.id as sectId, n.name as section, SUM(p.factPay) as summa FROM AppBundle:OrderPay p'.
                    ' JOIN p.order o'.
                    ' JOIN o.stage s'.
                    ' JOIN s.project j'.
                    ' JOIN j.gip u'.
                    ' JOIN j.Contractor c'.
                    ' JOIN o.section n'.
                    ' WHERE o.UserId='.$userId.' AND p.payDate >= :d1 AND p.payDate <= :d2'.
                    ' AND (o.isLegalEntity IS NULL OR o.isLegalEntity = 0)'.
                    ' GROUP BY id, project, section'
            ) ->setParameter('d1', $d1)->setParameter('d2', $d2)
            ->getResult();
        } else {
            $results = $this->getEntityManager()
            ->createQuery(
                'SELECT DISTINCT j.id as id, c.name as leg, j.name as project, u.fio as gip, n.id as sectId, n.name as section, SUM(p.factPay) as summa FROM AppBundle:OrderPay p'.
                    ' JOIN p.order o'.
                    ' JOIN o.stage s'.
                    ' JOIN s.project j'.
                    ' JOIN j.gip u'.
                    ' JOIN j.Contractor c'.
                    ' JOIN o.section n'.
                    ' WHERE o.UserId='.$userId.' AND j.ContractorId='.$legId.' AND p.payDate >= :d1 AND p.payDate <= :d2'.
                    ' AND (o.isLegalEntity IS NULL OR o.isLegalEntity = 0)'.
                    ' GROUP BY id, project, section'
            ) ->setParameter('d1', $d1)->setParameter('d2', $d2)
            ->getResult();
        }
        // Формирование массива с месяцами в обратном порядке
        $mns = array();
        $intervalM = new \DateInterval('P1M');
        for ($i = 1; $i <= 13; $i++) {
            $d1->add($intervalM);
            $mns[] = $d1->format('m.y');
        }
        $mns = array_reverse($mns);
        
        $projTable = array(); 
        foreach ($results as $result) {
            $proj = array();
            $proj['id'] = $result['id'];
            $proj['leg'] = $result['leg'];
            $proj['project'] = $result['project'];
            $proj['gip'] = $result['gip'];
            $proj['sectId'] = $result['sectId'];
            $proj['section'] = $result['section'];
            $proj['summa'] = $result['summa'];
            for ($i = 0; $i <= 12; $i++) {
                $proj[$mns[$i]] = 0;
            }
            $projTable[] = $proj;
        }

        return $projTable;
    }
    
    public function paysIspLastYearGroupedByMonth($userId, $legId = 0, \DateTime $dt2 = null)
    {
        
        if (!is_null($dt2)) {
            $d1 = clone $dt2;
            $d2 = clone $dt2;
        } else {
            $d1 = new \DateTime();
            $d2 = new \DateTime();
            $d2->modify('last day of this month');
        }
        $d1->modify('-1 year')->modify('first day of previous month');
        $d2->setTime(23,59,59);
        if ($legId > 0) {
            $results = $this->getEntityManager()
            ->createQuery(
            'SELECT'.
            ' SUM(p.factPay) as summa,'.
            ' j.id as projId,'.
            ' n.id as sectId,'.        
            ' SUBSTRING(p.payDate, 3, 5) as Month'.
            ' FROM AppBundle:OrderPay p'.
            ' JOIN p.order o'.
            ' JOIN o.section n'.        
            ' JOIN o.stage s'.
            ' JOIN s.project j'.
            ' WHERE o.UserId='.$userId.' AND j.ContractorId='.$legId.' AND (p.payDate >= :d1 AND p.payDate <= :d2)'.
            ' AND (o.isLegalEntity IS NULL OR o.isLegalEntity = 0)'.
            ' GROUP BY Month, projId, sectId ORDER BY Month, projId, sectId'
            ) ->setParameter('d1', $d1)->setParameter('d2', $d2)
            ->getResult();    
        } else {
            $results = $this->getEntityManager()
            ->createQuery(
            'SELECT'.
            ' SUM(p.factPay) as summa,'.
            ' j.id as projId,'.
            ' n.id as sectId,'.        
            ' SUBSTRING(p.payDate, 3, 5) as Month'.
            ' FROM AppBundle:OrderPay p'.
            ' JOIN p.order o'.
            ' JOIN o.section n'.        
            ' JOIN o.stage s'.
            ' JOIN s.project j'.
            ' WHERE o.UserId='.$userId.' AND (p.payDate >= :d1 AND p.payDate <= :d2)'.
            ' AND (o.isLegalEntity IS NULL OR o.isLegalEntity = 0)'.
            ' GROUP BY Month, projId, sectId ORDER BY Month, projId, sectId'
            ) ->setParameter('d1', $d1)->setParameter('d2', $d2)
            ->getResult();
        }
        $ProjTable = $this->projectsFromPeriodWithPays($userId, $d1, $d2, $legId);
        
        foreach ($results as $result) {
            $pid = $result['projId'];
            $sid = $result['sectId'];
            $summa = $result['summa'];
            $mn = substr($result['Month'],3,2).'.'.substr($result['Month'],0,2);
            foreach ($ProjTable as &$proj) {
                if ($proj['id'] === $pid and $proj['sectId'] === $sid) {
                    $proj[$mn] = $summa;
                    break;
                }
            }
        }

          return $ProjTable;
    }
    
    // Для отчета ГИП
    
    public function getProjectIdsFromPeriodGip($userId, $d1, $d2)
    {
        $results = $this->getEntityManager()
            ->createQuery(
            'SELECT'.
            ' DISTINCT j.id as id'.
            ' FROM AppBundle:OrderPay p'.
            ' JOIN p.order o'.
            ' JOIN o.stage s'.
            ' JOIN s.project j'.        
            ' WHERE j.GipId='.$userId.' AND ((s.FactEndDate >= :d1 AND s.FactEndDate <= :d2) OR'.
            ' (p.payDate >= :d1 AND p.payDate <= :d2))'.        
            ' ORDER BY id'
            ) 
            ->setParameter('d1', $d1)
            ->setParameter('d2', $d2)
            ->getResult();
        
        $ids = array();
        foreach ($results as $result) {
            $ids[] = $result['id'];
        }
        return $ids;
    }    
    
    public function costSumStagesFromPeriodGip($userId, $d1, $d2, $legId = null)
    {
        $results = $this->getEntityManager()
            ->createQuery(
            'SELECT'.
            ' SUM(s.cost) as summa,'.
            ' SUBSTRING(s.FactEndDate, 3, 5) as Month'.
            ' FROM AppBundle:ProjectStage s'.
            ' JOIN s.project j'.        
            ' WHERE'.
            (is_null($userId) ? '' : ' j.GipId='.$userId.' AND').
            ' (s.FactEndDate >= :d1 AND s.FactEndDate <= :d2)'.
            (is_null($legId) ? '' : ' AND j.ContractorId = '.$legId).
            ' GROUP BY Month ORDER BY Month'
            ) 
            ->setParameter('d1', $d1)
            ->setParameter('d2', $d2)
            ->getResult();
//         $resultsSub = $this->getEntityManager()
//            ->createQuery(
//            'SELECT'.
//            ' SUM(o.cost) as summa,'.
//            ' SUBSTRING(s.FactEndDate, 3, 5) as Month'.
//            ' FROM AppBundle:StageOrder o'.
//            ' JOIN o.stage s'.
//            ' JOIN s.project j'.        
//            ' WHERE j.GipId='.$userId.' AND o.isLegalEntity = 1 AND (s.FactEndDate >= :d1 AND s.FactEndDate <= :d2)'.
//            (is_null($legId) ? '' : ' AND j.ContractorId = '.$legId).        
//            ' GROUP BY Month ORDER BY Month'
//            ) 
//            ->setParameter('d1', $d1)
//            ->setParameter('d2', $d2)
//            ->getResult();
         
        $resultsSub = $this->getEntityManager()
            ->createQuery(
            'SELECT'.
            ' SUM(p.factPay) as summa,'.
            ' SUBSTRING(p.payDate, 3, 5) as Month'.
            ' FROM AppBundle:OrderPay p'.
            ' JOIN p.order o'.
            ' JOIN o.stage s'.
            ' JOIN s.project j'.        
            ' WHERE'.
            (is_null($userId) ? '' : ' j.GipId='.$userId.' AND').
            ' o.isLegalEntity = 1 AND (p.payDate >= :d1 AND p.payDate <= :d2)'.
            (is_null($legId) ? '' : ' AND j.ContractorId = '.$legId).        
            ' GROUP BY Month ORDER BY Month'
            ) 
            ->setParameter('d1', $d1)
            ->setParameter('d2', $d2)
            ->getResult(); 
         
        // Формирование массива с месяцами 
        $mns = array();
        $intervalM = new \DateInterval('P1M');
        for ($i = 1; $i <= 12; $i++) {
            $d1->add($intervalM);
            $mns[] = $d1->format('m.y');
        }
//        $mns = array_reverse($mns);
        
        $stagesSumByMonth = array();
        foreach ($mns as $mn) {
            $stagesSumByMonth[$mn] = 0;
            foreach ($results as $result) {
                if (substr($result['Month'],3,2).'.'.substr($result['Month'],0,2) === $mn) {
                        $stagesSumByMonth[$mn] = $result['summa'];
                        break;
                    }
            }
        }
        foreach ($mns as $mn) {
            foreach ($resultsSub as $result) {
                if (substr($result['Month'],3,2).'.'.substr($result['Month'],0,2) === $mn) {
                        $stagesSumByMonth[$mn] -= $result['summa'];
                        break;
                    }
            }
        }

        return $stagesSumByMonth;
    }
    
    public function paySumFromPeriodGip($userId, $d1, $d2, $legId = null)
    {
        $results = $this->getEntityManager()
            ->createQuery(
            'SELECT'.
            ' SUM(p.factPay) as summa,'.
            ' SUBSTRING(p.payDate, 3, 5) as Month'.
            ' FROM AppBundle:OrderPay p'.
            ' JOIN p.order o'.
            ' JOIN o.stage s'.
            ' JOIN s.project j'.        
            ' WHERE'.
            (is_null($userId) ? '' : ' j.GipId='.$userId.' AND').
            ' (p.payDate >= :d1 AND p.payDate <= :d2)'.
            ' AND (o.isLegalEntity IS NULL OR o.isLegalEntity = 0)'. 
            (is_null($legId) ? '' : ' AND j.ContractorId = '.$legId).        
            ' GROUP BY Month ORDER BY Month'
            )
            ->setParameter('d1', $d1)
            ->setParameter('d2', $d2)
            ->getResult();
        
        // Формирование массива с месяцами 
        $mns = array();
        $intervalM = new \DateInterval('P1M');
        for ($i = 1; $i <= 12; $i++) {
            $d1->add($intervalM);
            $mns[] = $d1->format('m.y');
        }
//        $mns = array_reverse($mns);
        
        $paysSumByMonth = array();
        foreach ($mns as $mn) {
            $paysSumByMonth[$mn] = 0;
            foreach ($results as $result) {
                if (substr($result['Month'],3,2).'.'.substr($result['Month'],0,2) === $mn) {
                        $paysSumByMonth[$mn] = $result['summa'];
                        break;
                    }
            }
        }

        return $paysSumByMonth;
    }
    
    public function costSumStagesBeforePeriodGip($userId, $d1, $legId = null)
    {
//        $ids = $this->getProjectIdsFromPeriodGip($userId, $d1, $d2);
        $result1 = $this->getEntityManager()
            ->createQuery(
            'SELECT'.
            ' SUM(s.cost) as summa'.
            ' FROM AppBundle:ProjectStage s'.
            ' JOIN s.project j'.        
            ' WHERE'.
            (is_null($userId) ? '' : ' j.GipId='.$userId.' AND').
            ' s.FactEndDate < :d1'.
            (is_null($legId) ? '' : ' AND j.ContractorId = '.$legId)
            ) 
            ->setParameter('d1', $d1)
            ->getSingleScalarResult();
//        $resultSub = $this->getEntityManager()
//            ->createQuery(
//            'SELECT'.
//            ' SUM(o.cost) as summa'.
//            ' FROM AppBundle:StageOrder o'.
//            ' JOIN o.stage s'.
//            ' JOIN s.project j'.        
//            ' WHERE j.GipId='.$userId.' AND o.isLegalEntity = 1 AND s.FactEndDate < :d1'.
//            (is_null($legId) ? '' : ' AND j.ContractorId = '.$legId)        
//            ) 
//            ->setParameter('d1', $d1)
//            ->getSingleScalarResult();
        
        $resultSub = $this->getEntityManager()
            ->createQuery(
            'SELECT'.
            ' SUM(p.factPay) as summa'.
            ' FROM AppBundle:OrderPay p'.
            ' JOIN p.order o'.
            ' JOIN o.stage s'.
            ' JOIN s.project j'.         
            ' WHERE'.
            (is_null($userId) ? '' : ' j.GipId='.$userId.' AND').
            ' o.isLegalEntity = 1 AND p.payDate < :d1'.
            (is_null($legId) ? '' : ' AND j.ContractorId = '.$legId)        
            ) 
            ->setParameter('d1', $d1)
            ->getSingleScalarResult();
        
         return ($result1 - $resultSub);
    }    
    
    public function paySumBeforePeriodGip($userId, $d1, $legId = null)
    {
//        $ids = $this->getProjectIdsFromPeriodGip($userId, $d1, $d2);
        $result = $this->getEntityManager()
            ->createQuery(
            'SELECT'.
            ' SUM(p.factPay) as summa'.
            ' FROM AppBundle:OrderPay p'.
            ' JOIN p.order o'.
            ' JOIN o.stage s'.
            ' JOIN s.project j'.        
            ' WHERE'.
            (is_null($userId) ? '' : ' j.GipId='.$userId.' AND').
            ' p.payDate < :d1'.
            (is_null($legId) ? '' : ' AND j.ContractorId = '.$legId).        
            ' AND (o.isLegalEntity IS NULL OR o.isLegalEntity = 0)'        
            ) 
            ->setParameter('d1', $d1)
            ->getSingleScalarResult();
        return $result;
    }
    
}
