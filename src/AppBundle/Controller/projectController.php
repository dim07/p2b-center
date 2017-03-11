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

/**
 * project controller.
 *
 */
class projectController extends Controller
{
    /**
     * Lists all project entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:project')->createQueryBuilder('e');
        
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
            'prev_message' => 'previous',
            'next_message' => 'next',
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
        $form   = $this->createForm('AppBundle\Form\projectType', $project);
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
        $deleteForm = $this->createDeleteForm($project);
        $editForm = $this->createForm('AppBundle\Form\projectType', $project);
        $new_stage = new ProjectStage();
        $new_stage->setProject($project);
        $newStageForm = $this->createForm('AppBundle\Form\ProjectStageType', $new_stage);
        
        $forms = array();
        $stages = $project->getStages();
        foreach($stages as $stage) {
            $new_order = new StageOrder();
            $new_order->setStage($stage);
            $newOrderForm = $this->createForm('AppBundle\Form\StageOrderType', $new_order)->createView();
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
    

}
