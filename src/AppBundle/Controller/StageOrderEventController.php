<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($stageOrderEvent);
            $em->flush();
            
            $editLink = $this->generateUrl('stageorderevent_edit', array('id' => $stageOrderEvent->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>New stageOrderEvent was created successfully.</a>" );
            
            $nextAction=  $request->get('submit') == 'save' ? 'stageorderevent' : 'stageorderevent_new';
            return $this->redirectToRoute($nextAction);
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
    

}
