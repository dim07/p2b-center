<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\User;
//use Doctrine\Common\Collections\ArrayCollection;
//use AppBundle\Form\Type\DateTimePickerType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * User controller.
 *
 */
class UserController extends Controller
{
    /**
     * Lists all User entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:User')->createQueryBuilder('e');
        
        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($users, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        return $this->render('user/index.html.twig', array(
            'users' => $users,
            'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView(),

        ));
    }
    
    /**
     * Lists all experts.
     *
     */
    public function expertsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:User')->createQueryBuilder('u')
                        ->leftJoin('u.legentity','l')
                        ->where('l.id is NULL OR ((l.customer IS NULL OR l.customer = 0) AND (l.contractor IS NULL OR l.contractor = 0))')    
                        ->orderBy('u.rating, u.fio', 'ASC');
        
        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($users, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        return $this->render('user/experts.html.twig', array(
            'users' => $users,
            'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView(),

        ));
    }
    

    /**
    * Create filter form and process filter request.
    *
    */
    protected function filter($queryBuilder, Request $request)
    {
        $session = $request->getSession();
        $filterForm = $this->createForm('AppBundle\Form\UserFilterType');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('UserControllerFilter');
        }

        // Filter action
        if ($request->get('filter_action') == 'filter') {
            // Bind values from the request
            $filterForm->handleRequest($request);

            if ($filterForm->isValid()) {
                // Build the query from the given form object
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
                // Save filter to session
                $filterData = $filterForm->getData();
                $session->set('UserControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
//            if ($session->has('UserControllerFilter')) {
//                $filterData = $session->get('UserControllerFilter');
//                
//                $em = $this->getDoctrine()->getManager();
//                
//                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
//                    if (is_object($filter) && $key !== 'sections') {
//                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
//                    }
//                    if ($key == 'sections') {
//                        $secs =  new ArrayCollection();
//                        foreach ($filterData['sections'] as $sec) {
//                            $secs[] = $em->getRepository('AppBundle:User')->findOneById($sec->getId());
//                        }
//                        $filterData['sections'] = $secs;
//                        
//                    }
//                }
//                
//
////                $filterForm = $this->createForm('AppBundle\Form\UserFilterType', $filterData);
////                 $filterForm->setData($filterData);
//                
//                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
//            }
        }

        return array($filterForm, $queryBuilder);
    }


    /**
    * Get results from paginator and get paginator view.
    *
    */
    protected function paginator($queryBuilder, Request $request)
    {
        //sorting
        $sortCol = $queryBuilder->getRootAlias().'.'.$request->get('pcg_sort_col', 'id');
        $queryBuilder->orderBy($sortCol, $request->get('pcg_sort_order', 'desc'));
        // Paginator
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($request->get('pcg_show' , 10));

        try {
            $pagerfanta->setCurrentPage($request->get('pcg_page', 1));
        } catch (\Pagerfanta\Exception\OutOfRangeCurrentPageException $ex) {
            $pagerfanta->setCurrentPage(1);
        }
        
        $entities = $pagerfanta->getCurrentPageResults();

        // Paginator - route generator
        $me = $this;
        $routeGenerator = function($page) use ($me, $request)
        {
            $requestParams = $request->query->all();
            $requestParams['pcg_page'] = $page;
            return $me->generateUrl('experts', $requestParams);
        };

        // Paginator - view
        $view = new TwitterBootstrap3View();
        $pagerHtml = $view->render($pagerfanta, $routeGenerator, array(
            'proximity' => 5,
            'prev_message' => '<i class = "fa fa-arrow-left"></i>',
            'next_message' => '<i class = "fa fa-arrow-right"></i>',
        ));

        return array($entities, $pagerHtml);
    }
    
    

    /**
     * Displays a form to create a new User entity.
     *
     */
    public function newAction(Request $request)
    {
    
        $user = new User();
        $form   = $this->createForm('AppBundle\Form\UserType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            $editLink = $this->generateUrl('user_edit', array('id' => $user->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>New user was created successfully.</a>" );
            
            $nextAction=  $request->get('submit') == 'save' ? 'user' : 'user_new';
            return $this->redirectToRoute($nextAction);
        }
        return $this->render('user/new.html.twig', array(
            'user' => $user,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a User entity.
     *
     */
    public function showAction(User $user)
    {
        $deleteForm = $this->createDeleteForm($user);
        return $this->render('user/show.html.twig', array(
            'user' => $user,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing User entity.
     *
     */
    public function editAction(Request $request, User $user)
    {
        $deleteForm = $this->createDeleteForm($user);
        $editForm = $this->createForm('AppBundle\Form\UserType', $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Edited Successfully!');
            return $this->redirectToRoute('user_edit', array('id' => $user->getId()));
        }
        return $this->render('user/edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a User entity.
     *
     */
    public function deleteAction(Request $request, User $user)
    {
    
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The User was deleted successfully');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the User');
        }
        
        return $this->redirectToRoute('user');
    }
    
    /**
     * Creates a form to delete a User entity.
     *
     * @param User $user The User entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $user->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete User by id
     *
     */
    public function deleteByIdAction(User $user){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $em->remove($user);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The User was deleted successfully');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the User');
        }

        return $this->redirect($this->generateUrl('user'));

    }
    

    /**
    * Bulk Action
    */
    public function bulkAction(Request $request)
    {
        $ids = $request->get("ids", array());
        $action = $request->get("bulk_action", "delete");

        if ($action == "delete") {
            try {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository('AppBundle:User');

                foreach ($ids as $id) {
                    $user = $repository->find($id);
                    $em->remove($user);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'users was deleted successfully!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the users ');
            }
        }

        return $this->redirect($this->generateUrl('user'));
    }
    
    
    public function reportPaysAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $data = array();
        $filterForm = $this->createForm('AppBundle\Form\reportUserPaysFilterType', 
            $data);
        if ($request->get('filter_action') == 'filter') {
            $filterForm->handleRequest($request);

            if ($filterForm->isSubmitted() && $filterForm->isValid()) {

                $data = $filterForm->getData();

            }
        }
        
        $pays = $em->getRepository('AppBundle:User')->paysIspLastYearGroupedByMonth($user->getId(), 0, array_key_exists('dt2',$data) ? $data['dt2'] : NULL);
        
        // Формирование массива с месяцами
        $mns = $this->get('monthes')->get13r(array_key_exists('dt2',$data) ? $data['dt2'] : NULL);

        $sums = array();
        foreach ($mns as $mn) {
            $sums[$mn] = 0;
            foreach ($pays as $pay) {
                $sums[$mn]+= (empty($pay[$mn])) ? 0 : $pay[$mn];
            }
        }
        
        $cnts = array();
        $n = 0;
        if (count($pays)>0) {
            $previd = $pays[0]['id'];
        }
        foreach ($pays as $pay) {
            if ($previd===$pay['id']) {
                $n++;
            } else {
                $cnts[] = $n;
                $n = 1;
                $previd = $pay['id'];
            }
        }
        $cnts[] = $n;
        
        return $this->render('user/report_pays.html.twig', array(
            'user' => $user,
            'mns' => $mns,
            'pays' => $pays,
            'sums' => $sums,
            'cnts' => $cnts,
            'filterForm' => $filterForm->createView(),
//            'dmp' => $dmp,
        ));
    }
    
    public function reportPays2Action(Request $request)
    {
        $user = $this->getUser();
//        $userId = $user->getId();
        $Legentity = $user->getLegentity();
        $em = $this->getDoctrine()->getManager();
        $legId = 0;
        $data = array();
        if ($Legentity){
            $legId = $Legentity->getId();
//            $data['leg'] = $Legentity;
        }
        $admin = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
        $filterForm = $this->createForm('AppBundle\Form\reportPaysFilterType', 
            $data, 
            array('user' => $user, 
                'admin' => $admin)
            );
        if ($request->get('filter_action') == 'filter') {
            $filterForm->handleRequest($request);

            if ($filterForm->isSubmitted() && $filterForm->isValid()) {

                $data = $filterForm->getData();
                // Для entity getData возвращает имена вместо Id, поэтому:
                if (array_key_exists('leg',$data)) {
                    $data['leg']=$request->get('leg');
                }    
                if (array_key_exists('user',$data)) {
                    $data['user']=$request->get('user');
                }
            }
        }    
        
        $pays = $em->getRepository('AppBundle:User')->paysIspLastYearGroupedByMonth(
                array_key_exists('user',$data) && $data['user']!=='' ? ($data['user'] instanceof \AppBundle\Entity\User ? $data['user']->getId() : intval($data['user'])) : 0, 
                $admin ? (array_key_exists('leg',$data) && $data['leg']!=='' ? ($data['leg'] instanceof \AppBundle\Entity\LegalEntity ? $data['leg']->getId() : intval($data['leg'])) : 0) : $legId,
                array_key_exists('dt2',$data) ? $data['dt2'] : NULL
                );
        
        // Формирование массива с месяцами
        $mns = $this->get('monthes')->get13r(array_key_exists('dt2',$data) ? $data['dt2'] : NULL);

        $sums = array();
        foreach ($mns as $mn) {
            $sums[$mn] = 0;
            foreach ($pays as $pay) {
                $sums[$mn]+= (empty($pay[$mn])) ? 0 : $pay[$mn];
            }
        }
        
        $cnts = array();
        $n = 0;
        if (count($pays)>0) {
            $previd = $pays[0]['id'];
        }
        foreach ($pays as $pay) {
            if ($previd===$pay['id']) {
                $n++;
            } else {
                $cnts[] = $n;
                $n = 1;
                $previd = $pay['id'];
            }
        }
        $cnts[] = $n;
        
        return $this->render('user/report_pays2.html.twig', array(
            'user' => $user,
            'admin' => $admin,
            'mns' => $mns,
            'pays' => $pays,
            'sums' => $sums,
            'cnts' => $cnts,
            'filterForm' => $filterForm->createView(),
        ));
    }
    
    
    public function reportGipAction(User $user)
    {
        $d1 = (new \DateTime('-1 year'))->modify('first day of this month');
//        $d2 = new \DateTime('last day of previous month');  
        $d2 = new \DateTime('last day of this month');
        
        
        $d2->setTime(23,59,59);
        $em = $this->getDoctrine()->getManager();
        $user_rep = $em->getRepository('AppBundle:User');
        $pays = $user_rep->paySumFromPeriodGip($user->getId(),new \DateTime($d1->format('Y-m-d H:i:sP')),new \DateTime($d2->format('Y-m-d H:i:sP')));
        $costs = $user_rep->costSumStagesFromPeriodGip($user->getId(),new \DateTime($d1->format('Y-m-d H:i:sP')),new \DateTime($d2->format('Y-m-d H:i:sP')));
        
        foreach ($costs as $key=>$cost) {
            $costs[$key] = $cost/1.18;
         }
        // Формирование массива с месяцами 
        $mns = $this->get('monthes')->get12();
        
        $narItog = array();
        $s1 = $user_rep->paySumBeforePeriodGip($user->getId(), $d1);
//        $s2 = $user_rep->costSumStagesBeforePeriodGip($user->getId(), $d1)/1.18;
        foreach ($mns as $mn) {
            $s1 += $pays[$mn];
//            $s2 += $costs[$mn];
            $d1->modify('first day of next month');
            $s2 = $user_rep->costSumStagesBeforePeriodGip($user->getId(), $d1)/1.18;
            if ($s2==0) {
                $narItog[$mn] = 0;
            } else {
                $narItog[$mn] = $s1/$s2;
            }    
        }
        
        return $this->render('user/report_gip.html.twig', array(
            'user' => $user,
            'mns' => $mns,
            'pays' => $pays,
            'costs' => $costs,
            'narItog' => $narItog,
        ));
    }
    
    public function reportGipWithFilterAction(Request $request)
    {
        $d1 = (new \DateTime('-1 year'))->modify('first day of this month');
        $d2 = new \DateTime('last day of this month');
        $d2->setTime(23,59,59);
        $em = $this->getDoctrine()->getManager();
        
        $user = $this->getUser();
        $Legentity = $user->getLegentity();
        $legId = 0;
        $data = array();
        if ($Legentity){
            $legId = $Legentity->getId();
//            $data['leg'] = $Legentity;
        }
        $admin = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
        $filterForm = $this->createForm('AppBundle\Form\reportGipFilterType', 
            $data, 
            array('user' => $user, 
                'adm' => $admin)
            );
        if ($request->get('filter_action') == 'filter') {
            $filterForm->handleRequest($request);

            if ($filterForm->isSubmitted() && $filterForm->isValid()) {

                $data = $filterForm->getData();
                // Для entity getData возвращает имена вместо Id, поэтому:
                if (array_key_exists('leg',$data)) {
                    $data['leg']=$request->get('leg');
                }    
                if (array_key_exists('gip',$data)) {
                    $data['gip']=$request->get('gip');
                }
            }
        } 
        
        $user_rep = $em->getRepository('AppBundle:User');
        $gipId = array_key_exists('gip',$data) && $data['gip']!=='' ? ($data['gip'] instanceof \AppBundle\Entity\User ? $data['gip']->getId() : intval($data['gip'])) : (($legId == 0) ? $user->getId() : null);
        $leg = array_key_exists('leg',$data) && $data['leg']!=='' ? ($data['leg'] instanceof \AppBundle\Entity\LegalEntity ? $data['leg']->getId() : intval($data['leg'])) : null;
        $pays = $user_rep->paySumFromPeriodGip(
            $gipId, 
            new \DateTime($d1->format('Y-m-d H:i:sP')),new \DateTime($d2->format('Y-m-d H:i:sP')), 
                $admin ? ((array_key_exists('status',$data) && $data['status']==1) ? $leg : null) : $legId);
        $costs = $user_rep->costSumStagesFromPeriodGip(
                $gipId,
                new \DateTime($d1->format('Y-m-d H:i:sP')),new \DateTime($d2->format('Y-m-d H:i:sP')), 
                $admin ? ((array_key_exists('status',$data) && $data['status']==1) ? $leg : null) : $legId);
         foreach ($costs as $key=>$cost) {
            $costs[$key] = $cost/1.18;
         }
        // Формирование массива с месяцами 
        $mns = $this->get('monthes')->get12();
        
        $narItog = array();
        $s1 = $user_rep->paySumBeforePeriodGip($gipId, $d1, $admin ? ((array_key_exists('status',$data) && $data['status']==1) ? $leg : null) : $legId);
//        $s2 = $user_rep->costSumStagesBeforePeriodGip($gipId, $d1, $admin ? ((array_key_exists('status',$data) && $data['status']==1) ? $leg : null) : $legId)/1.18;
        foreach ($mns as $mn) {
            $s1 += $pays[$mn];
//            $s2 += $costs[$mn];
            $d1->modify('first day of next month');
            $s2 = $user_rep->costSumStagesBeforePeriodGip($gipId, $d1, $admin ? ((array_key_exists('status',$data) && $data['status']==1) ? $leg : null) : $legId)/1.18;
            if ($s2==0) {
                $narItog[$mn] = 0;
            } else {
                $narItog[$mn] = $s1/$s2;
            }    
        }
        
        return $this->render('user/report_gip.html.twig', array(
            'user' => (!is_null($gipId) ? $user_rep->find($gipId) : $user),
            'gip' => $gipId,
            'legent' => (!is_null($leg) ? $em->getRepository('AppBundle:LegalEntity')->find($leg) : $user->getLegentity()),
            'mns' => $mns,
            'pays' => $pays,
            'costs' => $costs,
            'narItog' => $narItog,
            'filterForm' => $filterForm->createView(),
            'leg' => ($admin ? (array_key_exists('status',$data) && $data['status']==1 ? $leg : null) : $legId),
        ));
    }
    
    public function getGipsAjaxAction(Request $request)
    {
        $output=array();
        $em = $this->getDoctrine()->getManager();
        if ($request->isXmlHttpRequest()) {
            $leg_id = $request->request->get('leg');
            $qb = $em->getRepository('AppBundle:User')->createQueryBuilder('u')
                ->select('DISTINCT u')    
                ->innerJoin('u.gipprojects','j')
                ->innerJoin('j.Contractor','c')
                ->where('c.id = :id')    
                ->setParameter('id', $leg_id);
                     
            $gips = $qb->getQuery()->getResult();  
            foreach ($gips as $ob){
                $output[$ob->getId()] = $ob->getFio();
            }   
        }
        return new JsonResponse($output);
    }

}
