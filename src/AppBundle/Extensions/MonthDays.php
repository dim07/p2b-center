<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Extensions;

/**
 * Description of MonthDays
 *
 * @author dima
 */
class MonthDays {
    private $container;
    private $days;
    public function getDays(\DateTime $dt2 = null) {
        $days = array();
        $d2 = new \DateTime();
        $d1 = new \DateTime();
        if ($dt2!==null) {
            $d1 = clone $dt2;
            $d2 = clone $dt2;
        }
        $d1->modify('first day of this month');
        $d2->modify('last day of this month');
        while ($d1<=$d2) {
            $days[] = $d1;
            $d1->modify('next day');
        }
        return $days;
    }
    
}
