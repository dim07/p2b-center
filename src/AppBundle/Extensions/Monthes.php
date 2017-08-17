<?php

namespace AppBundle\Extensions;
//use Symfony\Component\DependencyInjection\Container;

/**
 * Description of Monthes
 *
 * @author dima
 */
class Monthes {
    private $container;
    private $mns;
    // We need to inject this variables later.
//    public function __construct(Container $container)
    public function __construct()        
    {
//        $this->em = $entityManager;
//        $this->container = $container;
//        $this->$mns = array();
    }
    
    public function get12(\DateTime $dt2 = null) {
        // Формирование массива с месяцами
        $mns = array();
        $d2 = new \DateTime();
        if ($dt2!==null) {
            $d2 = clone $dt2;
        }    
        $mns[] = $d2->format('m.y');
        for ($i = 1; $i <= 11; $i++) {
            $d2->modify('first day of previous month');
            $mns[] = $d2->format('m.y');
        }
        return array_reverse($mns);
    }

     
    public function get13() {
        // Формирование массива с месяцами
        $mns = array();
        $d2 = new \DateTime();
        $mns[] = $d2->format('m.y');
        for ($i = 1; $i <= 12; $i++) {
            $d2->modify('first day of previous month');
            $mns[] = $d2->format('m.y');
        }
        return array_reverse($mns);
    } 
    
    public function get14() {
        // Формирование массива с месяцами
        $mns = array();
        $mns[] = (new \DateTime('first day of next month'))->format('m.y');
        $d2 = new \DateTime();
        $mns[] = $d2->format('m.y');
        for ($i = 1; $i <= 12; $i++) {
            $d2->modify('first day of previous month');
            $mns[] = $d2->format('m.y');
        }
        return array_reverse($mns);
    }
    
    public function get12r() {
        // Формирование массива с месяцами
        $mns = array();
        $d2 = new \DateTime();
        $mns[] = $d2->format('m.y');
        for ($i = 1; $i <= 11; $i++) {
            $d2->modify('first day of previous month');
            $mns[] = $d2->format('m.y');
        }
        return $mns;
    }
     
    public function get13r(\DateTime $dt2 = null) {
        // Формирование массива с месяцами
        $mns = array();
        if (is_null($dt2)) {
            $d2 = new \DateTime();
        } else {
            $d2 = clone $dt2;
        }
        $mns[] = $d2->format('m.y');
        for ($i = 1; $i <= 12; $i++) {
            $d2->modify('first day of previous month');
            $mns[] = $d2->format('m.y');
        }
        return $mns;
    }
}
