<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\portfolio;

/**
 * portfolio controller.
 *
 */
class portfolioController extends Controller
{
    /**
     * Lists all portfolio entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:portfolio')->createQueryBuilder('e');
        
        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($portfolios, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        return $this->render('portfolio/index.html.twig', array(
            'portfolios' => $portfolios,
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
        $filterForm = $this->createForm('AppBundle\Form\portfolioFilterType');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('portfolioControllerFilter');
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
                $session->set('portfolioControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('portfolioControllerFilter')) {
                $filterData = $session->get('portfolioControllerFilter');
                
                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
                    if (is_object($filter)) {
                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
                    }
                }
                
                $filterForm = $this->createForm('AppBundle\Form\portfolioFilterType', $filterData);
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
            return $me->generateUrl('portfolio', $requestParams);
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
     * Displays a form to create a new portfolio entity.
     *
     */
    public function newAction(Request $request)
    {
    
        $portfolio = new portfolio();
        $form   = $this->createForm('AppBundle\Form\portfolioType', $portfolio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($portfolio);
            $em->flush();
            
            $editLink = $this->generateUrl('portfolio_edit', array('id' => $portfolio->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>New portfolio was created successfully.</a>" );
            
            $nextAction=  $request->get('submit') == 'save' ? 'portfolio' : 'portfolio_new';
            return $this->redirectToRoute($nextAction);
        }
        return $this->render('portfolio/new.html.twig', array(
            'portfolio' => $portfolio,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a portfolio entity.
     *
     */
    public function showAction(portfolio $portfolio)
    {
        $deleteForm = $this->createDeleteForm($portfolio);
        return $this->render('portfolio/show.html.twig', array(
            'portfolio' => $portfolio,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing portfolio entity.
     *
     */
    public function editAction(Request $request, portfolio $portfolio)
    {
        $deleteForm = $this->createDeleteForm($portfolio);
        $editForm = $this->createForm('AppBundle\Form\portfolioType', $portfolio);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($portfolio);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Edited Successfully!');
            return $this->redirectToRoute('portfolio_edit', array('id' => $portfolio->getId()));
        }
        return $this->render('portfolio/edit.html.twig', array(
            'portfolio' => $portfolio,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a portfolio entity.
     *
     */
    public function deleteAction(Request $request, portfolio $portfolio)
    {
    
        $form = $this->createDeleteForm($portfolio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($portfolio);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The portfolio was deleted successfully');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the portfolio');
        }
        
        return $this->redirectToRoute('portfolio');
    }
    
    /**
     * Creates a form to delete a portfolio entity.
     *
     * @param portfolio $portfolio The portfolio entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(portfolio $portfolio)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('portfolio_delete', array('id' => $portfolio->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete portfolio by id
     *
     */
    public function deleteByIdAction(portfolio $portfolio){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $em->remove($portfolio);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The portfolio was deleted successfully');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the portfolio');
        }

        return $this->redirect($this->generateUrl('portfolio'));

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
                $repository = $em->getRepository('AppBundle:portfolio');

                foreach ($ids as $id) {
                    $portfolio = $repository->find($id);
                    $em->remove($portfolio);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'portfolios was deleted successfully!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the portfolios ');
            }
        }

        return $this->redirect($this->generateUrl('portfolio'));
    }
    

}
