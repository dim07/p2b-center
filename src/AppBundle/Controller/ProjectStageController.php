<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\ProjectStage;
use AppBundle\Entity\StageOrder;

/**
 * ProjectStage controller.
 *
 */
class ProjectStageController extends Controller
{
    /**
     * Lists all ProjectStage entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:ProjectStage')->createQueryBuilder('e');
        
        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($projectStages, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        return $this->render('projectstage/index.html.twig', array(
            'projectStages' => $projectStages,
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
        $filterForm = $this->createForm('AppBundle\Form\ProjectStageFilterType');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('ProjectStageControllerFilter');
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
                $session->set('ProjectStageControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('ProjectStageControllerFilter')) {
                $filterData = $session->get('ProjectStageControllerFilter');
                
                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
                    if (is_object($filter)) {
                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
                    }
                }
                
                $filterForm = $this->createForm('AppBundle\Form\ProjectStageFilterType', $filterData);
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
            return $me->generateUrl('projectstage', $requestParams);
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
     * Displays a form to create a new ProjectStage entity.
     *
     */
    public function newAction(Request $request)
    {
    
        $projectStage = new ProjectStage();
        $form   = $this->createForm('AppBundle\Form\ProjectStageType', $projectStage);
        $form->handleRequest($request);
        
        $proj_id = $request->attributes->get('proj_id');
        $proj = $this->getDoctrine()
        ->getRepository('AppBundle:project')
        ->find($proj_id);
        if (!$proj) {
            throw $this->createNotFoundException(
                'No product found for id '.$proj_id
            );
        } else {
            $projectStage->setProject($proj);
        }
        
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($projectStage);
            $em->flush();
            
            $editLink = $this->generateUrl('projectstage_edit', array('id' => $projectStage->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>Новый этап проекта успешно создан.</a>" );
            
//            $nextAction=  $request->get('submit') == 'save' ? 'projectstage' : 'projectstage_new';
//            return $this->redirectToRoute($nextAction);
            return $this->redirectToRoute('project_edit',  array('id' => $proj_id));
        }
        return $this->render('projectstage/new.html.twig', array(
            'projectStage' => $projectStage,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a ProjectStage entity.
     *
     */
    public function showAction(ProjectStage $projectStage)
    {
        $deleteForm = $this->createDeleteForm($projectStage);
        return $this->render('projectstage/show.html.twig', array(
            'projectStage' => $projectStage,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing ProjectStage entity.
     *
     */
    public function editAction(Request $request, ProjectStage $projectStage)
    {
        $deleteForm = $this->createDeleteForm($projectStage);
        $editForm = $this->createForm('AppBundle\Form\ProjectStageType', $projectStage);
        $new_order = new StageOrder();
        $new_order->setStage($projectStage);
        $newOrderForm = $this->createForm('AppBundle\Form\StageOrderType', $new_order);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($projectStage);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Edited Successfully!');
            return $this->redirectToRoute('projectstage_edit', array('id' => $projectStage->getId()));
        }
        return $this->render('projectstage/edit.html.twig', array(
            'projectStage' => $projectStage,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'new_order_form' => $newOrderForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a ProjectStage entity.
     *
     */
    public function deleteAction(Request $request, ProjectStage $projectStage)
    {
    
        $form = $this->createDeleteForm($projectStage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($projectStage);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The ProjectStage was deleted successfully');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the ProjectStage');
        }
        
        return $this->redirectToRoute('projectstage');
    }
    
    /**
     * Creates a form to delete a ProjectStage entity.
     *
     * @param ProjectStage $projectStage The ProjectStage entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ProjectStage $projectStage)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('projectstage_delete', array('id' => $projectStage->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete ProjectStage by id
     *
     */
    public function deleteByIdAction(ProjectStage $projectStage){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $em->remove($projectStage);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The ProjectStage was deleted successfully');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the ProjectStage');
        }

        return $this->redirect($this->generateUrl('projectstage'));

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
                $repository = $em->getRepository('AppBundle:ProjectStage');

                foreach ($ids as $id) {
                    $projectStage = $repository->find($id);
                    $em->remove($projectStage);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'projectStages was deleted successfully!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the projectStages ');
            }
        }

        return $this->redirect($this->generateUrl('projectstage'));
    }
    

}
