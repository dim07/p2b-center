<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\LegalEntity;

/**
 * LegalEntity controller.
 *
 */
class LegalEntityController extends Controller
{
    /**
     * Lists all LegalEntity entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:LegalEntity')->createQueryBuilder('e');
        
        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($legalEntities, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        return $this->render('legalentity/index.html.twig', array(
            'legalEntities' => $legalEntities,
            'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView(),

        ));
    }
    
    /**
     * Lists all subcontractors.
     *
     */
    public function subcontractorsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:LegalEntity')->createQueryBuilder('l')
                        ->where('l.contractor = 1')    
                        ->orderBy('l.name', 'ASC');
        
        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($legalEntities, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        return $this->render('legalentity/index.html.twig', array(
            'legalEntities' => $legalEntities,
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
        $filterForm = $this->createForm('AppBundle\Form\LegalEntityFilterType');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('LegalEntityControllerFilter');
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
                $session->set('LegalEntityControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('LegalEntityControllerFilter')) {
                $filterData = $session->get('LegalEntityControllerFilter');
                
                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
                    if (is_object($filter)) {
                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
                    }
                }
                
                $filterForm = $this->createForm('AppBundle\Form\LegalEntityFilterType', $filterData);
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
            return $me->generateUrl('legalentity', $requestParams);
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
     * Displays a form to create a new LegalEntity entity.
     *
     */
    public function newAction(Request $request)
    {
    
        $legalEntity = new LegalEntity();
        $legalEntity->setUser($this->getUser());
        $form   = $this->createForm('AppBundle\Form\LegalEntityType', $legalEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($legalEntity);
            $em->flush();
            
            $editLink = $this->generateUrl('legalentity_edit', array('id' => $legalEntity->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>New legalEntity was created successfully.</a>" );
            
            $nextAction=  $request->get('submit') == 'save' ? 'legalentity' : 'legalentity_new';
            return $this->redirectToRoute($nextAction);
        }
        return $this->render('legalentity/new.html.twig', array(
            'legalEntity' => $legalEntity,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a LegalEntity entity.
     *
     */
    public function showAction(LegalEntity $legalEntity)
    {
        $deleteForm = $this->createDeleteForm($legalEntity);
        return $this->render('legalentity/show.html.twig', array(
            'legalEntity' => $legalEntity,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing LegalEntity entity.
     *
     */
    public function editAction(Request $request, LegalEntity $legalEntity)
    {
        $deleteForm = $this->createDeleteForm($legalEntity);
        $editForm = $this->createForm('AppBundle\Form\LegalEntityType', $legalEntity);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($legalEntity);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Edited Successfully!');
            return $this->redirectToRoute('legalentity_show', array('id' => $legalEntity->getId()));
        }
        return $this->render('legalentity/edit.html.twig', array(
            'legalEntity' => $legalEntity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a LegalEntity entity.
     *
     */
    public function deleteAction(Request $request, LegalEntity $legalEntity)
    {
    
        $form = $this->createDeleteForm($legalEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($legalEntity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The LegalEntity was deleted successfully');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the LegalEntity');
        }
        
        return $this->redirectToRoute('legalentity');
    }
    
    /**
     * Creates a form to delete a LegalEntity entity.
     *
     * @param LegalEntity $legalEntity The LegalEntity entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(LegalEntity $legalEntity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('legalentity_delete', array('id' => $legalEntity->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete LegalEntity by id
     *
     */
    public function deleteByIdAction(LegalEntity $legalEntity){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $em->remove($legalEntity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The LegalEntity was deleted successfully');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the LegalEntity');
        }

        return $this->redirect($this->generateUrl('legalentity'));

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
                $repository = $em->getRepository('AppBundle:LegalEntity');

                foreach ($ids as $id) {
                    $legalEntity = $repository->find($id);
                    $em->remove($legalEntity);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'legalEntities was deleted successfully!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the legalEntities ');
            }
        }

        return $this->redirect($this->generateUrl('legalentity'));
    }
    

}
