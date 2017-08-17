<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\project;
use AppBundle\Entity\ProjectStage;
use AppBundle\Entity\StageOrder;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PHPExcel_Style_Alignment;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
//use Symfony\Component\Security\Core\User\UserInterface;
//use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
//use AppBundle\Form\projectType;

/**
 * project controller.
 *
 */
class projectController extends Controller
{
    
//       public function userHasRole($id ,$role) {
//           // Entity manager
//           $em= $this->getDoctrine()->getManager();
//           $qb = $em->createQueryBuilder();
//
//           $qb->select('u')
//                   ->from('AppBundle:User', 'u') // Change this to the name of your bundle and the name of your mapped user Entity
//                   ->where('u.id = :user') 
//                   ->andWhere('u.roles LIKE :roles')
//                   ->setParameter('user', $id)
//                   ->setParameter('roles', '%"' . $role . '"%');
//
//           $user = $qb->getQuery()->getResult();
//
//           if(count($user) >= 1){
//              return true;
//           }else{
//              return false;
//           }
//       }
    
    
    /**
     * Lists all project entities.
     *
     */
    public function indexAllAction(Request $request)
    {
        if (!($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:project')->createQueryBuilder('e');

        
        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($projects, $pagerHtml) = $this->paginatorAll($queryBuilder, $request);
        
        return $this->render('project/index_all.html.twig', array(
            'projects' => $projects,
            'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView(),

        ));
    }
    
    /**
     * Lists all project entities.
     *
     */
    public function indexAction(Request $request)
    {
        $user = $this->getUser();
        $userId = $user->getId();
        $Legentity = $user->getLegentity();
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:project')->createQueryBuilder('e');

        if (!$Legentity) {
            $queryBuilder->where('e.GipId = '.$userId);
        } else {
            $legId = $Legentity->getId();
            $queryBuilder->where('e.CustomerId = '.$legId.' or e.ContractorId = '.$legId);
        }   
        
        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($projects, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        return $this->render('project/index.html.twig', array(
            'projects' => $projects,
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
        $filterForm = $this->createForm('AppBundle\Form\projectFilterType');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('projectControllerFilter');
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
                $session->set('projectControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('projectControllerFilter')) {
                $filterData = $session->get('projectControllerFilter');
                
                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
                    if (is_object($filter)) {
                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
                    }
                }
                
                $filterForm = $this->createForm('AppBundle\Form\projectFilterType', $filterData);
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
            }
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
            return $me->generateUrl('project', $requestParams);
        };

        // Paginator - view
        $view = new TwitterBootstrap3View();
        $pagerHtml = $view->render($pagerfanta, $routeGenerator, array(
            'proximity' => 3,
            'prev_message' => '<i class = "fa fa-arrow-left"></i>',
            'next_message' => '<i class = "fa fa-arrow-right"></i>',
        ));

        return array($entities, $pagerHtml);
    }
    
    protected function paginatorAll($queryBuilder, Request $request)
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
            return $me->generateUrl('allprojects', $requestParams);
        };

        // Paginator - view
        $view = new TwitterBootstrap3View();
        $pagerHtml = $view->render($pagerfanta, $routeGenerator, array(
            'proximity' => 3,
            'prev_message' => '<i class = "fa fa-arrow-left"></i>',
            'next_message' => '<i class = "fa fa-arrow-right"></i>',
        ));

        return array($entities, $pagerHtml);
    }
    
    

    /**
     * Displays a form to create a new project entity.
     *
     */
    public function newAction(Request $request)
    {
    
        $project = new project();
        $user = $this->getUser();
        $form   = $this->createForm('AppBundle\Form\projectType', $project, array('user' => $user));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();
            
            $editLink = $this->generateUrl('project_edit', array('id' => $project->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>New project was created successfully.</a>" );
            
            $nextAction=  $request->get('submit') == 'save' ? 'project' : 'project_new';
            return $this->redirectToRoute($nextAction);
        }
        return $this->render('project/new.html.twig', array(
            'project' => $project,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a project entity.
     *
     */
    public function showAction(project $project)
    {
        $deleteForm = $this->createDeleteForm($project);
        return $this->render('project/show.html.twig', array(
            'project' => $project,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing project entity.
     *
     */
    public function editAction(Request $request, project $project)
    {
//        @var $AuthorizationChecker \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface 
//        $AuthorizationChecker \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
        $user = $this->getUser();
        $userId = $user->getId();
        $leg = $user->getlegentity();
        $leg_id = -1;
        if ($leg) {
            $leg_id = $leg->getId();
        }
        if (!(//$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') or 
                $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') or
                $userId===$project->getGipId() or 
                $leg_id===$project->getContractorId() or 
                $leg_id===$project->getCustomerId())) {
            throw $this->createAccessDeniedException();
        }
//        $dmp = array();
//        $dmp['userId'] = $userId;
//        $dmp['ContrId'] = $project->getContractorId();
//        $dmp['CustId'] = $project->getCustomerId();
//        $dmp['admin'] = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
//        $dmp['Contractor'] = ($userId===$project->getContractorId());
//        $dmp['Customer'] = ($userId===$project->getCustomerId());
        $deleteForm = $this->createDeleteForm($project);
        $editForm = $this->createForm('AppBundle\Form\projectType', $project, array('user' => $user));
//        $editForm = $this->createForm(projectType::class, $project);
        $new_stage = new ProjectStage();
        $new_stage->setProject($project);
        $newStageForm = $this->createForm('AppBundle\Form\ProjectStageType', $new_stage);
        
        $forms = array();
        $stages = $project->getStages();
        foreach($stages as $stage) {
            $new_order = new StageOrder();
            $new_order->setStage($stage);
            $newOrderForm = $this->createForm('AppBundle\Form\StageOrderType', $new_order, array('user' => $user))->createView();
            $forms['order'.$stage->getId()] = $newOrderForm; 
        }    
        
        $editForm->handleRequest($request);
        
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Edited Successfully!');
            return $this->redirectToRoute('project_edit', array('id' => $project->getId()));
        }
        return $this->render('project/edit.html.twig', array(
            'project' => $project,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'new_stage_form' => $newStageForm->createView(),
            'forms' => $forms,
//            'dmp' => $dmp,
        ));
    }
    
    

    /**
     * Deletes a project entity.
     *
     */
    public function deleteAction(Request $request, project $project)
    {
    
        $form = $this->createDeleteForm($project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($project);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The project was deleted successfully');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the project');
        }
        
        return $this->redirectToRoute('project');
    }
    
    /**
     * Creates a form to delete a project entity.
     *
     * @param project $project The project entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(project $project)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('project_delete', array('id' => $project->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete project by id
     *
     */
    public function deleteByIdAction(project $project){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $em->remove($project);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The project was deleted successfully');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the project');
        }

        return $this->redirect($this->generateUrl('project'));

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
                $repository = $em->getRepository('AppBundle:project');

                foreach ($ids as $id) {
                    $project = $repository->find($id);
                    $em->remove($project);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'projects was deleted successfully!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the projects ');
            }
        }

        return $this->redirect($this->generateUrl('project'));
    }
    
    public function reportMoneyAction(Request $request, project $project)
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
        $project->setDatas($em->getRepository('AppBundle:project')->paysLastYearGroupedByMonth($project->getId(), 1, array_key_exists('dt2',$data) ? $data['dt2'] : NULL));
        $project->setDatas1($em->getRepository('AppBundle:project')->paysLastYearGroupedByMonth($project->getId(), 0, array_key_exists('dt2',$data) ? $data['dt2'] : NULL));
        $repS = $em->getRepository('AppBundle:ProjectStage');
        foreach ($project->getStages() as $stage) {
            $stage->setDatas($repS->paysLastYearGroupedByMonth($stage->getId(), array_key_exists('dt2',$data) ? $data['dt2'] : NULL));
            $rep = $em->getRepository('AppBundle:StageOrder');
            foreach ($stage->getOrders() as $order) {
                $order->setDatas($rep->paysLastYearGroupedByMonth($order->getId(), array_key_exists('dt2',$data) ? $data['dt2'] : NULL));
            }
        }    
        // Формирование массива с месяцами
         $mns = $this->get('monthes')->get12(array_key_exists('dt2',$data) ? $data['dt2'] : NULL);

        // Формирование массива для расчета дохода помесячно
        $stagesSumByMonth = array();
        foreach ($mns as $mn) {
            $stagesSumByMonth[$mn] = 0;
            foreach ($project->getStages() as $stage) {
                $fpd = $stage->getFactEndDate();
                if (!$fpd==NULL and $fpd->format('m.y') === $mn) {
                    $stagesSumByMonth[$mn] += $stage->getCost();
                }
            }
        }    
        // Найти ГИП и посчитать ему план стоимость
        $orderGip = NULL;
        $sumCostofStages = 0;
        $sumCostofLeg = 0;
        $sumCostofIsp = 0;
        
//        $dmp = array();
        
        foreach ($project->getStages() as $stage) {
            $sumCostofStages += $stage->getCost();
            foreach ($stage->getOrders() as $order) {
                if ($order->getIsLegalEntity()) {
                    $sumCostofLeg += $order->getCost();
                } else {
//                    if ($order->getIdSection() == 131 and $order->getUserId() === $project->getGipId()) {
                    if ($order->getIdSection() == 131) {
                        $orderGip = $order;
                    } else {
                        $sumCostofIsp += $order->getCost();
                    } 
                }    
            }
        }
        
        if ($orderGip) { 
//            $dmp['sumCostofStages'] = $sumCostofStages;
//            $dmp['sumCostofLeg'] = $sumCostofLeg;
//            $dmp['sumCostofIsp'] = $sumCostofIsp;
            $orderGip->setCost(round(($sumCostofStages-$sumCostofLeg)/1.18*$project->getNofot()- $sumCostofIsp,2));
        }
        
        return $this->render('project/report_money.html.twig', array(
            'project' => $project,
            'mns' => $mns,
            'stagesSumByMonth' => $stagesSumByMonth,
            'filterForm' => $filterForm->createView(),
//            'dmp' => $dmp,
        ));
    }
    
    public function statementsAction(Request $request, project $project)
    {
        $user = $this->getUser();
        $userId = $user->getId();
        if (!($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') or 
                $userId===$project->getGipId() 
//                or 
//                $userId===$project->getContractorId() or 
//                $userId===$project->getCustomerId()
                )) 
        {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();
        $repJ = $em->getRepository('AppBundle:project');
        $isChargDate = $repJ->isCurChargDate($project->getId());
        $repS = $em->getRepository('AppBundle:ProjectStage');
        $repO = $em->getRepository('AppBundle:StageOrder');
        
        $dt1 = new \DateTime('-1 year');
        $dt2 = new \DateTime();
        if ($isChargDate || (int)($dt2->format('d'))>15) {
            $dt1->modify('first day of next month');
            $d = clone $dt2;
            $dt2->modify('first day of next month');
            if ((int)($d->format('d'))>15) {
                $this->get('session')->getFlashBag()->add('danger', 'После 15 числа можно заявить только на следующий месяц, заявка выполняется на <b>'.$dt2->format('d.m.Y').'</b>');
            } else {
                $this->get('session')->getFlashBag()->add('danger', 'Так как по проекту уже утверждены выплаты за текущий месяц, заявка выполняется на <b>'.$dt2->format('d.m.Y').'</b>');
            }    
        }        
        $project->setDatas($repJ->paysFromPeriodGroupedByMonth($project->getId(), 1, $dt1, $dt2));
        $project->setDatas1($repJ->paysFromPeriodGroupedByMonth($project->getId(), 0, $dt1, $dt2));

        foreach ($project->getStages() as $stage) {
            $stage->setDatas($repS->paysFromPeriodGroupedByMonth($stage->getId(), $dt1, $dt2));
            foreach ($stage->getOrders() as $order) {
                $order->setDatas($repO->paysFromPeriodGroupedByMonth($order->getId(), $dt1, $dt2));
            }
        }
            
        // Формирование массива с месяцами
        $mns = $this->get('monthes')->get12($dt2);

        // Формирование массива для расчета дохода помесячно
        $stagesSumByMonth = array();
        foreach ($mns as $mn) {
            $stagesSumByMonth[$mn] = 0;
            foreach ($project->getStages() as $stage) {
                $fpd = $stage->getFactEndDate();
                if (!$fpd==NULL and $fpd->format('m.y') === $mn) {
                    $stagesSumByMonth[$mn] += $stage->getCost();
                }
            }
        }    
        // Найти ГИП и посчитать ему план стоимость
        $orderGip = NULL;
        $sumCostofStages = 0;
        $sumCostofLeg = 0;
        $sumCostofIsp = 0;
        // Массив для заполнения формы
        $data0 = array();
        $mn0 = $dt2->format('m.y');//(new \DateTime())->format('m.y');
        
        foreach ($project->getStages() as $stage) {
            $sumCostofStages += $stage->getCost();
            foreach ($stage->getOrders() as $order) {
                if ($order->getIsLegalEntity()) {
                    $sumCostofLeg += $order->getCost();
                } else {
                    if ($order->getIdSection() == 131) {
                        $orderGip = $order;
                    } else {
                        $sumCostofIsp += $order->getCost();
                    } 
                    ////для формы
                    foreach ($order->getPays() as $pay) {
                        if ($pay->getStatementDate()->format('m.y')===$mn0) {
                            $pp = $pay->getPlanPay(); 
                            if ($pp) {
                                $data0['planPay'.$order->getId()] = $pp;
                            }
                        }
                    }
                }    
            }
        }
        
        if ($orderGip) { 
            $orderGip->setCost(round(($sumCostofStages-$sumCostofLeg)/1.18*$project->getNofot()- $sumCostofIsp,2));
        }
        
        ////Делаем форму
        
        $fb = $this->createFormBuilder($data0);
        foreach ($project->getStages() as $stage) {
            $sumCostofStages += $stage->getCost();
            foreach ($stage->getOrders() as $order) {
                if (!$order->getIsLegalEntity()) {
                   $fb->add('planPay'.$order->getId(), MoneyType::class, array(
                                'label' => '', 
                                'currency' => 'RUB',
                                'required' => false
                            )); 
                }    
            }
        }
        $form = $fb->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $form->getData();
            $new_st_cnt = 0;
            $upd_st_cnt = 0;
            foreach ($data as $key => $value) {
                $id = (int)substr($key,7);
                $order = $repO->find($id);

                if ($order) {
                    $found = FALSE;
                    foreach ($order->getPays() as $pay) {
                        $std = $pay->getStatementDate();
                        if ($std && $std->format('m.y')===$mn0) {
                            $pp = $pay->getPlanPay(); 
                            $found = TRUE;
                            if (!($pp == $value)) {
                                $pay->setPlanPay($value);
                                $pay->setStatementDate($dt2);
                                $em->persist($pay);
                                //$em->flush();
                                $upd_st_cnt++;
                            }
                            break;
                        }
                    }
                    if (!$found and $value) {
                        $pay = new \AppBundle\Entity\OrderPay();
                        $pay->setOrder($order);
                        $pay->setPlanPay($value);
                        $pay->setStatementDate($dt2);
                        $em->persist($pay);
                        //$em->flush();
                        $new_st_cnt++;
                    }    
                }
            }
            if ($new_st_cnt > 0 or $upd_st_cnt > 0) {
                $em->flush();
                if ($new_st_cnt > 0) {
                    $this->get('session')->getFlashBag()->add('success', 'Вновь созданных заявок на выплату: '.$new_st_cnt);
                }
                if ($upd_st_cnt > 0) {
                    $this->get('session')->getFlashBag()->add('success', 'Изменено заявок на выплату: '.$upd_st_cnt);
                }
            } else {
                $this->get('session')->getFlashBag()->add('info', 'Изменений в заявках на выплату не произведено.');
            }
            
        }
        
        return $this->render('project/statements.html.twig', array(
            'project' => $project,
            'mns' => $mns,
            'stagesSumByMonth' => $stagesSumByMonth,
            'form' => $form->createView(),
        ));
    }   
    
    
    public function paysConfirmAction(Request $request, project $project)
    {
        $user = $this->getUser();
        $userId = $user->getId();
        if (!($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') or 
//                $userId===$project->getGipId() or 
                $userId===$project->getContractorId() or 
                $userId===$project->getCustomerId())) {
            throw $this->createAccessDeniedException();
        }
        
        $em = $this->getDoctrine()->getManager();
        $project->setDatas($em->getRepository('AppBundle:project')->paysLastYearGroupedByMonth($project->getId(), 1));
        $project->setDatas1($em->getRepository('AppBundle:project')->paysLastYearGroupedByMonth($project->getId(), 0));
        $repS = $em->getRepository('AppBundle:ProjectStage');
        foreach ($project->getStages() as $stage) {
            $stage->setDatas($repS->paysLastYearGroupedByMonth($stage->getId()));
            $rep = $em->getRepository('AppBundle:StageOrder');
            foreach ($stage->getOrders() as $order) {
                $order->setDatas($rep->paysLastYearGroupedByMonth($order->getId()));
            }
        }    
        // Формирование массива с месяцами
        $mns = $this->get('monthes')->get12();
        // Формирование массива для расчета дохода помесячно
        $stagesSumByMonth = array();
        foreach ($mns as $mn) {
            $stagesSumByMonth[$mn] = 0;
            foreach ($project->getStages() as $stage) {
                $fpd = $stage->getFactEndDate();
                if (!$fpd==NULL and $fpd->format('m.y') === $mn) {
                    $stagesSumByMonth[$mn] += $stage->getCost();
                }
            }
        }    
        // Найти ГИП и посчитать ему план стоимость
        $orderGip = NULL;
        $sumCostofStages = 0;
        $sumCostofLeg = 0;
        $sumCostofIsp = 0;
        // Массив для заполнения формы
        $data0 = array();
        $mn0 = (new \DateTime())->format('m.y');
        
        foreach ($project->getStages() as $stage) {
            $sumCostofStages += $stage->getCost();
            foreach ($stage->getOrders() as $order) {
                if ($order->getIsLegalEntity()) {
                    $sumCostofLeg += $order->getCost();
                } else {
                    if ($order->getIdSection() == 131) {
                        $orderGip = $order;
                    } else {
                        $sumCostofIsp += $order->getCost();
                    } 
                    ////для формы
                    foreach ($order->getPays() as $pay) {
                        if (($pay->getStatementDate() and $pay->getStatementDate()->format('m.y')===$mn0) or 
                                ($pay->getChargDate() and $pay->getChargDate()->format('m.y')===$mn0)) {
                            $pp = $pay->getPlanPay(); 
                            if ($pp) {
                                $data0['planPay'.$order->getId()] = $pp;
                            }
                            $fp = $pay->getFactPay(); 
                            if ($fp) {
                                $data0['factPay'.$order->getId()] = $fp;
                            } elseif ($pp) {
                                $data0['factPay'.$order->getId()] = $pp;
                            }
                        }
                    }
                }    
            }
        }
        
        if ($orderGip) { 
            $orderGip->setCost(round(($sumCostofStages-$sumCostofLeg)/1.18*$project->getNofot()- $sumCostofIsp,2));
        }
        
        ////Делаем форму
        
        $fb = $this->createFormBuilder($data0);
        foreach ($project->getStages() as $stage) {
            $sumCostofStages += $stage->getCost();
            foreach ($stage->getOrders() as $order) {
                if (!$order->getIsLegalEntity()) {
                   $fb->add('planPay'.$order->getId(), MoneyType::class, array(
                                'label' => '', 
                                'currency' => 'RUB',
                                'required' => false,
                                'disabled' => true
                            ));
                   $fb->add('factPay'.$order->getId(), MoneyType::class, array(
                                'label' => '', 
                                'currency' => 'RUB',
                                'required' => false
                            ));
                }    
            }
        }
        $form = $fb->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $new_st_cnt = 0;
            $upd_st_cnt = 0;
            foreach ($data as $key => $value) {
                $pos = strpos($key, 'fact');
                if ($pos !== false) {
                    $id = (int)substr($key,7);
                    $order = $this->getDoctrine()
                        ->getRepository('AppBundle:StageOrder')
                        ->find($id);

                    if ($order) {
                        $found = FALSE;
                        foreach ($order->getPays() as $pay) {
                            $chd = $pay->getChargDate();
                            if ($chd !== NULL && $chd->format('m.y')===$mn0) {
                                $fp = $pay->getFactPay(); 
                                $found = TRUE;
                                if (!($fp == $value)) {
                                    $pay->setFactPay($value);
                                    $pay->setChargDate(new \DateTime());
                                    $em->persist($pay);
                                    //$em->flush();
                                    $upd_st_cnt++;
                                }
                                break;
                            }
                        }
                        if (!$found and $value) {
                            $pay = new \AppBundle\Entity\OrderPay();
                            $pay->setOrder($order);
                            $pay->setFactPay($value);
                            $pay->setChargDate(new \DateTime());
                            $em->persist($pay);
                            //$em->flush();
                            $new_st_cnt++;
                        }    
                    }
                }    
            }
            if ($new_st_cnt > 0 or $upd_st_cnt > 0) {
                $em->flush();
                if ($new_st_cnt > 0) {
                    $this->get('session')->getFlashBag()->add('success', 'Вновь созданных утверждений на выплату: '.$new_st_cnt);
                }
                if ($upd_st_cnt > 0) {
                    $this->get('session')->getFlashBag()->add('success', 'Изменено утверждений на выплату: '.$upd_st_cnt);
                }
            } else {
                $this->get('session')->getFlashBag()->add('info', 'Изменений в утверждениях на выплату не произведено.');
            }
            
        }
        
        return $this->render('project/confirmations.html.twig', array(
            'project' => $project,
            'mns' => $mns,
            'stagesSumByMonth' => $stagesSumByMonth,
            'form' => $form->createView(),
        ));
    }
    
    
    public function reportMoneyExcelAction(project $project)
    {
        $em = $this->getDoctrine()->getManager();
        $project->setDatas($em->getRepository('AppBundle:project')->paysLastYearGroupedByMonth($project->getId(), 1));
        $project->setDatas1($em->getRepository('AppBundle:project')->paysLastYearGroupedByMonth($project->getId(), 0));
        $repS = $em->getRepository('AppBundle:ProjectStage');
        foreach ($project->getStages() as $stage) {
            $stage->setDatas($repS->paysLastYearGroupedByMonth($stage->getId()));
            $rep = $em->getRepository('AppBundle:StageOrder');
            foreach ($stage->getOrders() as $order) {
                $order->setDatas($rep->paysLastYearGroupedByMonth($order->getId()));
            }
        }    
        
        // ask the service for a Excel5
       $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

       $phpExcelObject->getProperties()->setCreator("liuggio");
//           ->setLastModifiedBy("Giulio De Donato")
//           ->setTitle("Office 2005 XLSX Test Document")
//           ->setSubject("Office 2005 XLSX Test Document")
//           ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
//           ->setKeywords("office 2005 openxml php")
//           ->setCategory("Test result file");
       $sheet = $phpExcelObject->setActiveSheetIndex(0);
       $center_style = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        $sheet->getStyle("A1:N1")->applyFromArray($center_style);
        $sheet
                ->mergeCells('A1:N1')
                ->mergeCells('A2:N2')
                ->mergeCells('A3:N3')
                ->setCellValue('A1', 'Доходы и расходы по проекту')
                ->setCellValue('A2', '"'.$project->getName().'"')
                ->setCellValue('A3', 'на '.date("d.m.Y"));

       
//       $phpExcelObject->getActiveSheet()->setTitle('Simple');
       // Set active sheet index to the first sheet, so Excel opens this as the first sheet
       $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'reportMoney'.$project->getId().'.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

}
