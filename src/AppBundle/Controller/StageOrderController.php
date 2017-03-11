<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\StageOrder;

/**
 * StageOrder controller.
 *
 */
class StageOrderController extends Controller
{
    /**
     * Lists all StageOrder entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:StageOrder')->createQueryBuilder('e');
        
        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($stageOrders, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
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
                $session->set('StageOrderControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('StageOrderControllerFilter')) {
                $filterData = $session->get('StageOrderControllerFilter');
                
                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
                    if (is_object($filter)) {
                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
                    }
                }
                
                $filterForm = $this->createForm('AppBundle\Form\StageOrderFilterType', $filterData);
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
            return $me->generateUrl('stageorder', $requestParams);
        };

        // Paginator - view
        $view = new TwitterBootstrap3View();
        $pagerHtml = $view->render($pagerfanta, $routeGenerator, array(
            'proximity' => 3,
            'prev_message' => '<',
            'next_message' => '>',
        ));

        return array($entities, $pagerHtml);
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
        
        $form   = $this->createForm('AppBundle\Form\StageOrderType', $stageOrder);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($stageOrder);
            $em->flush();
            
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
    
    

    /**
     * Displays a form to edit an existing StageOrder entity.
     *
     */
    public function editAction(Request $request, StageOrder $stageOrder)
    {
        $deleteForm = $this->createDeleteForm($stageOrder);
        $editForm = $this->createForm('AppBundle\Form\StageOrderType', $stageOrder);
        
        $new_pay = new \AppBundle\Entity\OrderPay();
        $new_pay->setOrder($stageOrder);
        $newPayForm = $this->createForm('AppBundle\Form\OrderPayType', $new_pay);
        
      
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($stageOrder);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Edited Successfully!');
            return $this->redirectToRoute('stageorder_edit', array('id' => $stageOrder->getId()));
        }
        return $this->render('stageorder/edit.html.twig', array(
            'stageOrder' => $stageOrder,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'new_pay_form' => $newPayForm->createView(),
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
            $em->remove($stageOrder);
            $em->flush();
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
