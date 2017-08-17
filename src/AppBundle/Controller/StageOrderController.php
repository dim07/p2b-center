<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\StageOrder;
use AppBundle\Entity\Offer;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
//use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * StageOrder controller.
 *
 */
class StageOrderController extends Controller
{
    public function userHasRole($id ,$role) {
           // Entity manager
           $em= $this->getDoctrine()->getManager();
           $qb = $em->createQueryBuilder();

           $qb->select('u')
                   ->from('AppBundle:User', 'u') // Change this to the name of your bundle and the name of your mapped user Entity
                   ->where('u.id = :user') 
                   ->andWhere('u.roles LIKE :roles')
                   ->setParameter('user', $id)
                   ->setParameter('roles', '%"' . $role . '"%');

           $user = $qb->getQuery()->getResult();

           if(count($user) >= 1){
              return true;
           }else{
              return false;
           }
       }
    
    /**
     * Lists all StageOrder entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:StageOrder')->createQueryBuilder('e');
        
        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($stageOrders, $pagerHtml) = $this->paginator($queryBuilder, $request,'stageorder');
        
        return $this->render('stageorder/index.html.twig', array(
            'stageOrders' => $stageOrders,
            'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView(),

        ));
    }
    
    public function order_persAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:StageOrder')->createQueryBuilder('e')
                ->where('(e.isLegalEntity = 0 or e.isLegalEntity is null) and e.isPublic = 1')
                ->leftJoin('e.pays','p')
                ->select('e')
                ->addSelect('SUM(p.factPay) as factPay')
                ->groupBy('e.id');
        
//        if ($request->query->has('user_id')) {
//            $user_id = $request->query->get('user_id');
//            $queryBuilder->andWhere('e.UserId = '.$user_id);
//        } 
        
        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($stageOrders, $pagerHtml) = $this->paginator($queryBuilder, $request, 'order_pers');
//        dump($this->generateUrl($request->get('_route'), $request->query->all()));
        $forms = array();
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            foreach($stageOrders as $order) {
                $new_offer = new Offer();
//                $new_offer->setOrder($order[0]);
//                $new_offer->setUserId($this->getUser()->getId());
                $newOfferForm = $this->createForm('AppBundle\Form\OfferType', $new_offer);
                $formview = $newOfferForm->createView();
                $forms['offer'.$order[0]->getId()] = $formview; 
                
//                $newOfferForm->handleRequest($request);
//                if ($newOfferForm->isSubmitted() && $newOfferForm->isValid()) {
//                    $em = $this->getDoctrine()->getManager();
//                    $em->persist($new_offer);
//                    $em->flush();
//
//                    $editLink = $this->generateUrl('offer_edit', array('id' => $new_offer->getId()));
//                    $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>Новое предложение было добавлено.</a>" );
//                    
////                    return $this->redirect($request->headers->get('referer'));
//                    return new RedirectResponse($request->headers->get('referer'));
////                    return $this->redirect($this->generateUrl($request->get('_route'), $request->query->all()));
//                }
            }
            
        }
        
        return $this->render('stageorder/order_pers.html.twig', array(
            'stageOrders' => $stageOrders,
            'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView(),
            'forms' => $forms,
        ));
    }
    
    public function order_userAction(Request $request)
    {
        $user = $this->getUser();
        $user_id = $user->getId();
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:StageOrder')->createQueryBuilder('e')
                ->where('e.UserId = '.$user_id.' and (e.isLegalEntity = 0 or e.isLegalEntity is null) and e.isPublic = 1')
                ->leftJoin('e.pays','p')
                ->leftJoin('e.stage','s')
                ->leftJoin('s.project','j')
                ->select('e')
                ->addSelect('SUM(p.factPay) as factPay')
                ->addSelect('j.GipId as gipId')
                ->groupBy('e.id');
                
        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($stageOrders, $pagerHtml) = $this->paginator($queryBuilder, $request, 'order_user');
        $forms = array();
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            foreach($stageOrders as $order) {
                $new_offer = new Offer();
                $newOfferForm = $this->createForm('AppBundle\Form\OfferType', $new_offer);
                $formview = $newOfferForm->createView();
                $forms['offer'.$order[0]->getId()] = $formview; 
            }
        }
        
        return $this->render('stageorder/order_user.html.twig', array(
            'stageOrders' => $stageOrders,
            'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView(),
            'forms' => $forms,
        ));
    }
    
    public function order_subAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:StageOrder')->createQueryBuilder('e')
                ->where('e.isLegalEntity = 1 and e.isPublic = 1');
        
        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($stageOrders, $pagerHtml) = $this->paginator($queryBuilder, $request, 'order_sub');
        
        return $this->render('stageorder/index.html.twig', array(
            'stageOrders' => $stageOrders,
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
        $filterForm = $this->createForm('AppBundle\Form\StageOrderFilterType');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('StageOrderControllerFilter');
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
                if ($filterData['status'] == 1) {
                    $queryBuilder->andWhere('e.UserId is null');
                } 
                elseif ($filterData['status'] == 2) {
                    $queryBuilder->andWhere('e.UserId is not null and e.startDate <= CURRENT_DATE() and (e.factEndDate is null or e.factEndDate > CURRENT_DATE())');
                }
                elseif ($filterData['status'] == 3) {
                    $queryBuilder->andWhere('e.factEndDate is not null and e.factEndDate <= CURRENT_DATE())');
                }
                $session->set('StageOrderControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
//            if ($session->has('StageOrderControllerFilter')) {
//                $filterData = $session->get('StageOrderControllerFilter');
//                
//                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
//                    if (is_object($filter)) {
//                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
//                    }
//                }
//                
//                $filterForm = $this->createForm('AppBundle\Form\StageOrderFilterType', $filterData);
//                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
//            }
        }

        return array($filterForm, $queryBuilder);
    }


    /**
    * Get results from paginator and get paginator view.
    *
    */
    protected function paginator($queryBuilder, Request $request, $route)
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
        $routeGenerator = function($page) use ($me, $request, $route)
        {
            $requestParams = $request->query->all();
            $requestParams['pcg_page'] = $page;
            return $me->generateUrl($route, $requestParams);
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
    
    private function UpdateGipPlanCost(\AppBundle\Entity\project $project) {
        // Найти ГИП и посчитать ему план стоимость
        $orderGip = NULL;
        $sumCostofStages = 0;
        $sumCostofLeg = 0;
        $sumCostofIsp = 0;
        
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
            $orderGip->setCost(round(($sumCostofStages-$sumCostofLeg)/1.18*$project->getNofot()- $sumCostofIsp,2));
            $em = $this->getDoctrine()->getManager();
            $em->persist($orderGip);
            $em->flush();
        }
    }

    /**
     * Displays a form to create a new StageOrder entity.
     *
     */
    public function newAction(Request $request)
    {
    
        $stageOrder = new StageOrder();
//        $form   = $this->createForm('AppBundle\Form\StageOrderType', $stageOrder);
//        $form->handleRequest($request);
        
        $stage_id = $request->attributes->get('stage_id');
        $redir_proj = $request->attributes->get('redir_proj');
        $stage = $this->getDoctrine()
        ->getRepository('AppBundle:ProjectStage')
        ->find($stage_id);
        if (!$stage) {
            throw $this->createNotFoundException(
                'Нет этапа для id '.$stage_id
            );
        } else {
            $stageOrder->setStage($stage);
        }
        $user = $this->getUser();
        $form   = $this->createForm('AppBundle\Form\StageOrderType', $stageOrder, array('user' => $user));
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($stageOrder);
            $em->flush();
            
            $this->UpdateGipPlanCost($stage->getProject());
            
            $editLink = $this->generateUrl('stageorder_edit', array('id' => $stageOrder->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>Новый заказ успешно создан.</a>" );
            
//            $nextAction=  $request->get('submit') == 'save' ? 'stageorder' : 'stageorder_new';
//            return $this->redirectToRoute($nextAction);
            if ($redir_proj == 1) {
                return $this->redirectToRoute('project_edit',  array('id' => $stage->getIdProject()));
            } else {
                return $this->redirectToRoute('projectstage_edit',  array('id' => $stage_id));
            }
        }
        return $this->render('stageorder/new.html.twig', array(
            'stageOrder' => $stageOrder,
            'form'   => $form->createView(),
        ));
    }
    
    /**
     * Finds and displays a StageOrder entity.
     *
     */
    public function showAction(StageOrder $stageOrder)
    {
        $deleteForm = $this->createDeleteForm($stageOrder);
        return $this->render('stageorder/show.html.twig', array(
            'stageOrder' => $stageOrder,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    private function CreateChOfferForm(StageOrder $stageOrder, $offerId)
    {
        return $this->createFormBuilder($stageOrder)
        ->add('offerId', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, array(
            'data' => $offerId,
        ))
        ->getForm();
    }
            
    public function chooseOfferAction(Request $request, StageOrder $stageOrder)
    {
        $offerId = $request->get('offer_id');
        $form = $this->CreateChOfferForm($stageOrder, $offerId);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $stageOrder->setOfferId($offerId);
            
            $offer = $this->getDoctrine()
            ->getRepository('AppBundle:Offer')
            ->find($offerId);
            if (!$offer) {
                throw $this->createNotFoundException(
                    'Нет предложения для id '.$offerId
                );
            } else {
                if ($stageOrder->getIsLegalEntity()) {
                    $stageOrder->setContractor($offer->getLegal());
                } else {
                    $stageOrder->setUserIsp($offer->getUser());
                }
            }
            
            $em->persist($stageOrder);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Выбрано предложение для работы!');
            return $this->redirectToRoute('stageorder_edit', array('id' => $stageOrder->getId()));
        }
        return $this->render('stageorder/edit.html.twig', array(
            'stageOrder' => $stageOrder,
            'ch_offer_form' => $form->createView(),
        ));
        
    }        

    /**
     * Displays a form to edit an existing StageOrder entity.
     *
     */
    public function editAction(Request $request, StageOrder $stageOrder)
    {
        $user = $this->getUser();
        $userId = $user->getId();
        $project = $stageOrder->getStage()->getProject();
        if (!($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') or 
                $userId===$project->getGipId() or 
                $userId===$project->getContractorId() or 
                $userId===$project->getCustomerId())) {
            throw $this->createAccessDeniedException();
        }
        $deleteForm = $this->createDeleteForm($stageOrder);
        $editForm = $this->createForm('AppBundle\Form\StageOrderType', $stageOrder, array('user' => $user));
        
        $new_pay = new \AppBundle\Entity\OrderPay();
        $new_pay->setOrder($stageOrder);
        $newPayForm = $this->createForm('AppBundle\Form\OrderPayType', $new_pay);
        
        $new_event = new \AppBundle\Entity\StageOrderEvent();
        $new_event->setOrder($stageOrder);
        $newEventForm = $this->createForm('AppBundle\Form\StageOrderEventType', $new_event);
        
        $ofrForms = array();
        $offers = $stageOrder->getOffers();
        foreach($offers as $offer) {
            $ofrForm = $this->CreateChOfferForm($stageOrder, $offer->getId())->createView();
            $ofrForms['offer'.$offer->getId()] = $ofrForm; 
        }
      
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($stageOrder);
            $em->flush();
            
            $this->UpdateGipPlanCost($stageOrder->getStage()->getProject());
            
            $this->get('session')->getFlashBag()->add('success', 'Заказ успешно изменен!');
            return $this->redirectToRoute('stageorder_edit', array('id' => $stageOrder->getId()));
        }
        return $this->render('stageorder/edit.html.twig', array(
            'stageOrder' => $stageOrder,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'new_pay_form' => $newPayForm->createView(),
            'new_event_form' => $newEventForm->createView(),
            'ofrForms' => $ofrForms,
        ));
    }
    
    

    /**
     * Deletes a StageOrder entity.
     *
     */
    public function deleteAction(Request $request, StageOrder $stageOrder)
    {
    
        $form = $this->createDeleteForm($stageOrder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $proj = $stageOrder->getStage()->getProject();
            $em->remove($stageOrder);
            $em->flush();
            
            $this->UpdateGipPlanCost($proj);
            
            $this->get('session')->getFlashBag()->add('success', 'The StageOrder was deleted successfully');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the StageOrder');
        }
        
        return $this->redirectToRoute('stageorder');
    }
    
    /**
     * Creates a form to delete a StageOrder entity.
     *
     * @param StageOrder $stageOrder The StageOrder entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(StageOrder $stageOrder)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('stageorder_delete', array('id' => $stageOrder->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete StageOrder by id
     *
     */
    public function deleteByIdAction(StageOrder $stageOrder){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $em->remove($stageOrder);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The StageOrder was deleted successfully');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the StageOrder');
        }

        return $this->redirect($this->generateUrl('stageorder'));

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
                $repository = $em->getRepository('AppBundle:StageOrder');

                foreach ($ids as $id) {
                    $stageOrder = $repository->find($id);
                    $em->remove($stageOrder);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'stageOrders was deleted successfully!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the stageOrders ');
            }
        }

        return $this->redirect($this->generateUrl('stageorder'));
    }
    

}
