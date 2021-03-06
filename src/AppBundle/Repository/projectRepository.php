<?php

namespace AppBundle\Repository;

/**
 * projectRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class projectRepository extends \Doctrine\ORM\EntityRepository
{
    public function paysGroupedByMonth($projId, $year, $le)
    {
        $pays = array();
        $st = '';
        if ($le==1) {
            $st = 'AND o.isLegalEntity = 1';
        } else {
            $st = 'AND (o.isLegalEntity IS NULL OR o.isLegalEntity = 0)';
        }
            
        $results = $this->getEntityManager()
            ->createQuery(
                'SELECT SUM(p.factPay) as summa, SUBSTRING(p.payDate, 6, 2) as Month FROM AppBundle:OrderPay p'.
                    ' JOIN p.order o'.
                    ' JOIN o.stage s'.
                    ' WHERE s.idProject='.$projId.' AND SUBSTRING(p.payDate, 1, 4) = '.$year.
                    $st.
                    ' GROUP BY Month'
            )
            ->getResult();
        foreach ($results as $result) {
            $pays[$result['Month']] = $result['summa'];
        }
//        $pays['summa'] = $this->sumOfPays($orderId);
        return $pays;
    }
    
    public function paysLastYearGroupedByMonth($projId, $le, \DateTime $dt2 = null)
    {
        if (is_null($dt2)) {
            $d2 = new \DateTime('last day of this month');
            $d2->setTime(23,59,59);
            $d1 = new \DateTime('-1 year');
            $d1->modify('first day of this month');
        } else {
            $d2 = clone $dt2;
            $d1 = clone $dt2;
            $d1->modify('-1 year');
            $d1->modify('first day of this month');
        }     
        $pays = array();
        $st = '';
        if ($le==1) {
            $st = 'AND o.isLegalEntity = 1';
        } else {
            $st = 'AND (o.isLegalEntity IS NULL OR o.isLegalEntity = 0)';
        }
            
        $results = $this->getEntityManager()
            ->createQuery(
                'SELECT SUM(p.factPay) as summa, SUBSTRING(p.payDate, 3, 5) as Month FROM AppBundle:OrderPay p'.
                    ' JOIN p.order o'.
                    ' JOIN o.stage s'.
                    ' WHERE s.idProject='.$projId.' AND p.payDate >= :d1 AND p.payDate <= :d2 '.
                    $st.
                    ' GROUP BY Month ORDER BY Month'
            ) ->setParameter('d1', $d1)->setParameter('d2', $d2)
            ->getResult();
        foreach ($results as $result) {
            $pays[substr($result['Month'],3,2).'.'.substr($result['Month'],0,2)] = $result['summa'];
        }
        return $pays;
    }
    
    public function isCurChargDate($projId)
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT COUNT(p.id) FROM AppBundle:OrderPay p'
            . ' JOIN p.order o'
            . ' JOIN o.stage s'
            . ' WHERE s.idProject='.$projId
            . ' AND (o.isLegalEntity IS NULL OR o.isLegalEntity = 0)'
            . ' AND SUBSTRING(p.chargDate, 6, 2)='.(new \DateTime())->format('m')
            . ' AND SUBSTRING(p.chargDate, 1, 4)='.(new \DateTime())->format('Y')
        );
        $count = $query->getSingleScalarResult();
        return $count > 0;
    }
    
    public function paysFromPeriodGroupedByMonth($projId, $le, \DateTime $dt1, \DateTime $dt2)
    {
        $pays = array();
        $st = '';
        if ($le==1) {
            $st = 'AND o.isLegalEntity = 1';
        } else {
            $st = 'AND (o.isLegalEntity IS NULL OR o.isLegalEntity = 0)';
        }
            
        $results = $this->getEntityManager()
            ->createQuery(
                'SELECT SUM(p.factPay) as summa, SUBSTRING(p.payDate, 3, 5) as Month FROM AppBundle:OrderPay p'.
                    ' JOIN p.order o'.
                    ' JOIN o.stage s'.
                    ' WHERE s.idProject='.$projId.' AND p.payDate >= :d1 AND p.payDate <= :d2 '.
                    $st.
                    ' GROUP BY Month ORDER BY Month'
            ) ->setParameters(array('d1' => $dt1,'d2' => $dt2))
            ->getResult();
        foreach ($results as $result) {
            $pays[substr($result['Month'],3,2).'.'.substr($result['Month'],0,2)] = $result['summa'];
        }
        return $pays;
    }
}
