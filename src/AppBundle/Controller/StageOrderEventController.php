<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\StageOrderEvent;

/**
 * StageOrderEvent controller.
 *
 */
class StageOrderEventController extends Controller
{
    public function reportAction(Request $request)
    {
        $user = $this->getUser();
        $userId = $user->getId();
        $Legentity = $user->getLegentity();
        $em = $this->getDoctrine()->getManager();
        $adm = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
        $rep = $em->getRepository('AppBundle:StageOrderEvent');
        $data = array();
        if (!$Legentity) {
           $data['gip'] = $user;//$userId;
           $data['leg'] = NULL;
        } else {
            $legId = $Legentity->getId();
            $data['gip'] = NULL;
            $data['leg'] = $Legentity;//$legId;
        }
        
        $filterForm = $this->createForm('AppBundle\Form\reportEventFilterType', $data, array('user' => $user, 'adm' => $adm));
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
                    if (array_key_exists('project',$data)) {
                        $data['project']=$request->get('project');
                    }  
                    if (array_key_exists('EventType',$data)) {
                        $data['EventType']=$request->get('EventType');
                    }
            }
        }    
        if ($adm) {
           $evs = $rep->eventsFromPeriod(
                array_key_exists('leg',$data) && $data['leg']!=='' ? ($data['leg'] instanceof \AppBundle\Entity\LegalEntity ? $data['leg']->getId() : intval($data['leg'])) : NULL, 
                array_key_exists('gip',$data) && $data['gip']!=='' ? ($data['gip'] instanceof \AppBundle\Entity\User ? $data['gip']->getId() : intval($data['gip'])) : NULL,
                array_key_exists('project',$data) && $data['project']!=='' ? ($data['project'] instanceof \AppBundle\Entity\project ? $data['project']->getId() : intval($data['project'])) : NULL, 
                array_key_exists('EventType',$data) && $data['EventType']!=='' ? ($data['EventType'] instanceof \AppBundle\Entity\StageOrderEvent ? $data['EventType']->getId() : intval($data['EventType'])) : NULL,    
                array_key_exists('dt1',$data) ? $data['dt1'] : NULL, 
                array_key_exists('dt2',$data) ? $data['dt2'] : NULL); 
        } elseif (!$Legentity) {
           $evs = $rep->eventsFromPeriod(
                array_key_exists('leg',$data) && $data['leg']!=='' ? ($data['leg'] instanceof \AppBundle\Entity\LegalEntity ? $data['leg']->getId() : intval($data['leg'])) : NULL, 
                $userId, 
                array_key_exists('project',$data) && $data['project']!=='' ? ($data['project'] instanceof \AppBundle\Entity\project ? $data['project']->getId() : intval($data['project'])) : NULL, 
                array_key_exists('EventType',$data) && $data['EventType']!=='' ? ($data['EventType'] instanceof \AppBundle\Entity\StageOrderEvent ? $data['EventType']->getId() : intval($data['EventType'])) : NULL,   
                array_key_exists('dt1',$data) ? $data['dt1'] : NULL, 
                array_key_exists('dt2',$data) ? $data['dt2'] : NULL);
        } else {
            $evs = $rep->eventsFromPeriod(
                $legId, 
                array_key_exists('gip',$data) && $data['gip']!=='' ? ($data['gip'] instanceof \AppBundle\Entity\User ? $data['gip']->getId() : intval($data['gip'])) : NULL, 
                array_key_exists('project',$data) && $data['project']!=='' ? ($data['project'] instanceof \AppBundle\Entity\project ? $data['project']->getId() : intval($data['project'])) : NULL, 
                array_key_exists('EventType',$data) && $data['EventType']!=='' ? ($data['EventType'] instanceof \AppBundle\Entity\StageOrderEvent ? $data['EventType']->getId() : intval($data['EventType'])) : NULL,
                array_key_exists('dt1',$data) ? $data['dt1'] : NULL, 
                array_key_exists('dt2',$data) ? $data['dt2'] : NULL);
        }
//        $evs = $rep->eventsFromPeriod(
//                array_key_exists('leg',$data) && $data['leg']!=='' ? ($data['leg'] instanceof \AppBundle\Entity\LegalEntity ? $data['leg']->getId() : intval($data['leg'])) : NULL, 
//                array_key_exists('gip',$data) && $data['gip']!=='' ? ($data['gip'] instanceof \AppBundle\Entity\User ? $data['gip']->getId() : intval($data['gip'])) : NULL, 
//                array_key_exists('project',$data) && $data['project']!=='' ? ($data['project'] instanceof \AppBundle\Entity\project ? $data['project']->getId() : intval($data['project'])) : NULL, 
//                array_key_exists('dt1',$data) ? $data['dt1'] : NULL, 
//                array_key_exists('dt2',$data) ? $data['dt2'] : NULL);
        
        return $this->render('stageorderevent/report.html.twig', array(
            'evs' => $evs,
            'filterForm' => $filterForm->createView(),
        ));
    }
    
    public function reportProjectAction(Request $request)
    {
        $user = $this->getUser();
        $userId = $user->getId();
        $Legentity = $user->getLegentity();
        $legId = 0;
        if ($Legentity) {
            $legId = $Legentity->getId();
        }
        $em = $this->getDoctrine()->getManager();
//        $proj_id = $request->get('proj_id');
        $proj_id = $request->query->get('proj_id');
        $project = $this->getDoctrine()->getRepository('AppBundle:project')->find($proj_id);
        if (!$project) {
            throw $this->createNotFoundException(
                'Не найден проект с id = '.$proj_id
            );
        }
                
        if (!($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') or
                $userId===$project->getGipId() or 
                $legId===$project->getContractorId() or 
                $legId===$project->getCustomerId())) {
            throw $this->createAccessDeniedException();
        }
        
        $rep = $em->getRepository('AppBundle:StageOrderEvent');
        $data = array();
        
        $filterForm = $this->createForm('AppBundle\Form\projectEventFilterType', $data, array('proj_id' => $proj_id));
        if ($request->get('filter_action') == 'filter') {
            $filterForm->handleRequest($request);

            if ($filterForm->isSubmitted() && $filterForm->isValid()) {

                    $data = $filterForm->getData();
                    if (array_key_exists('EventType',$data)) {
                        $data['EventType']=$request->get('EventType');
                    }
            }
        }
        
        $evs = $rep->eventsFromProject($proj_id,
                array_key_exists('EventType',$data) && $data['EventType']!=='' ? ($data['EventType'] instanceof \AppBundle\Entity\StageOrderEvent ? $data['EventType']->getId() : intval($data['EventType'])) : NULL,
                array_key_exists('dt1',$data) ? $data['dt1'] : NULL, 
                array_key_exists('dt2',$data) ? $data['dt2'] : NULL);
        
        return $this->render('stageorderevent/report_project.html.twig', array(
            'evs' => $evs,
            'project' => $project,
            'filterForm' => $filterForm->createView(),
        ));
    }
    
    /**
     * Lists all StageOrderEvent entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:StageOrderEvent')->createQueryBuilder('e');
        
        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($stageOrderEvents, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        return $this->render('stageorderevent/index.html.twig', array(
            'stageOrderEvents' => $stageOrderEvents,
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
        $filterForm = $this->createForm('AppBundle\Form\StageOrderEventFilterType');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('StageOrderEventControllerFilter');
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
                $session->set('StageOrderEventControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('StageOrderEventControllerFilter')) {
                $filterData = $session->get('StageOrderEventControllerFilter');
                
                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
                    if (is_object($filter)) {
                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
                    }
                }
                
                $filterForm = $this->createForm('AppBundle\Form\StageOrderEventFilterType', $filterData);
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
            return $me->generateUrl('stageorderevent', $requestParams);
        };

        // Paginator - view
        $view = new TwitterBootstrap3View();
        $pagerHtml = $view->render($pagerfanta, $routeGenerator, array(
            'proximity' => 3,
            'prev_message' => 'previous',
            'next_message' => 'next',
        ));

        return array($entities, $pagerHtml);
    }
    
    

    /**
     * Displays a form to create a new StageOrderEvent entity.
     *
     */
    public function newAction(Request $request)
    {
    
        $stageOrderEvent = new StageOrderEvent();
        $form   = $this->createForm('AppBundle\Form\StageOrderEventType', $stageOrderEvent);
        $form->handleRequest($request);
        
        $order_id = $request->attributes->get('order_id');
        $order = $this->getDoctrine()
        ->getRepository('AppBundle:StageOrder')
        ->find($order_id);
        if (!$order) {
            throw $this->createNotFoundException(
                'Нет заказа для id '.$order_id
            );
        } else {
            $stageOrderEvent->setOrder($order);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($stageOrderEvent);
            $em->flush();
            
            $editLink = $this->generateUrl('stageorderevent_edit', array('id' => $stageOrderEvent->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>Новое событие по заказу успешно создано.</a>" );
            
//            $nextAction=  $request->get('submit') == 'save' ? 'stageorderevent' : 'stageorderevent_new';
//            return $this->redirectToRoute($nextAction);
            return $this->redirectToRoute('stageorder_edit',  array('id' => $order_id));
        }
        return $this->render('stageorderevent/new.html.twig', array(
            'stageOrderEvent' => $stageOrderEvent,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a StageOrderEvent entity.
     *
     */
    public function showAction(StageOrderEvent $stageOrderEvent)
    {
        $deleteForm = $this->createDeleteForm($stageOrderEvent);
        return $this->render('stageorderevent/show.html.twig', array(
            'stageOrderEvent' => $stageOrderEvent,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing StageOrderEvent entity.
     *
     */
    public function editAction(Request $request, StageOrderEvent $stageOrderEvent)
    {
        $deleteForm = $this->createDeleteForm($stageOrderEvent);
        $editForm = $this->createForm('AppBundle\Form\StageOrderEventType', $stageOrderEvent);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($stageOrderEvent);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Edited Successfully!');
            return $this->redirectToRoute('stageorderevent_edit', array('id' => $stageOrderEvent->getId()));
        }
        return $this->render('stageorderevent/edit.html.twig', array(
            'stageOrderEvent' => $stageOrderEvent,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a StageOrderEvent entity.
     *
     */
    public function deleteAction(Request $request, StageOrderEvent $stageOrderEvent)
    {
    
        $form = $this->createDeleteForm($stageOrderEvent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($stageOrderEvent);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The StageOrderEvent was deleted successfully');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the StageOrderEvent');
        }
        
        return $this->redirectToRoute('stageorderevent');
    }
    
    /**
     * Creates a form to delete a StageOrderEvent entity.
     *
     * @param StageOrderEvent $stageOrderEvent The StageOrderEvent entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(StageOrderEvent $stageOrderEvent)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('stageorderevent_delete', array('id' => $stageOrderEvent->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete StageOrderEvent by id
     *
     */
    public function deleteByIdAction(StageOrderEvent $stageOrderEvent){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $em->remove($stageOrderEvent);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The StageOrderEvent was deleted successfully');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the StageOrderEvent');
        }

        return $this->redirect($this->generateUrl('stageorderevent'));

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
                $repository = $em->getRepository('AppBundle:StageOrderEvent');

                foreach ($ids as $id) {
                    $stageOrderEvent = $repository->find($id);
                    $em->remove($stageOrderEvent);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'stageOrderEvents was deleted successfully!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the stageOrderEvents ');
            }
        }

        return $this->redirect($this->generateUrl('stageorderevent'));
    }
    
    public function getProjectsAjaxAction(Request $request)
    {
        $output=array();
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $Legentity = $user->getLegentity();
//      $gip_id = $user->getId();
        $leg_id = 0;
        if (!is_null($Legentity)) {
            $leg_id =  $Legentity->getId();
        }
        if ($request->isXmlHttpRequest()) {
            $gip_id = $request->request->get('gip');
            $gip = $em->getRepository('AppBundle:User')->find($gip_id);
            if ($gip) {
                $projects = $gip->getGipprojects();
                foreach ($projects as $ob){
                    if ($ob->getContractorId()==$leg_id) {
                        $output[$ob->getId()] = $ob->getName();
                    }
                }
            } else {
                $qb = $em->getRepository('AppBundle:project')->createQueryBuilder('j');
                            $qb->innerJoin('j.Contractor','l')
                               ->where('l.id = :id')    
                               ->setParameter('id', $leg_id);   
                $projects = $qb->getQuery()->getResult();  
                foreach ($projects as $ob){
                    $output[$ob->getId()] = $ob->getName();
                }
            }   
        }
        return new JsonResponse($output);
    }
    

}
