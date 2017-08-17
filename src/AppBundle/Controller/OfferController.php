<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\Offer;
use AppBundle\Entity\StageOrder;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Offer controller.
 *
 */
class OfferController extends Controller
{
    /**
     * Lists all Offer entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:Offer')->createQueryBuilder('e');
        
        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($offers, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        return $this->render('offer/index.html.twig', array(
            'offers' => $offers,
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
        $filterForm = $this->createForm('AppBundle\Form\OfferFilterType');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('OfferControllerFilter');
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
                $session->set('OfferControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('OfferControllerFilter')) {
                $filterData = $session->get('OfferControllerFilter');
                
                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
                    if (is_object($filter)) {
                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
                    }
                }
                
                $filterForm = $this->createForm('AppBundle\Form\OfferFilterType', $filterData);
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
            return $me->generateUrl('offer', $requestParams);
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
     * Displays a form to create a new Offer entity.
     *
     */
    public function newAction(Request $request)
    {
    
        $offer = new Offer();
        $form   = $this->createForm('AppBundle\Form\OfferType', $offer);
        $form->handleRequest($request);
        
        $order_id = $request->attributes->get('order_id');
        $order = $this->getDoctrine()
        ->getRepository('AppBundle:StageOrder')
        ->find($order_id);
        $user = $this->getUser();
        $Legentity = $user->getLegentity();
        if (!$order) {
            throw $this->createNotFoundException(
                'Нет заказа для id '.$order_id
            );
        } else {
//            $user = $this->getUser();
            $offer->setOrder($order);
            if ($order->getIsLegalEntity()) {
//                $Legentity = getLegentity();
                if (!$Legentity) {
                    throw $this->createNotFoundException(
                        'Нет юрлица для пользователя с Id '.$user->getId()
                    );
                }    
                else {
                    //$offer->setLegalId($Legentity->getId());
                    $offer->setLegal($Legentity);
                }    
            } else {
//                $offer->setUserId($user->getId());
                $offer->setUser($user);
            }
                
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($offer);
            $em->flush();
            
            $editLink = $this->generateUrl('offer_edit', array('id' => $offer->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>Новое предложение было добавлено.</a>" );
            
//            $nextAction=  $request->get('submit') == 'save' ? 'offer' : 'offer_new';
//            return $this->redirectToRoute($nextAction);
            
//            if ($order->getIsLegalEntity()) {
//                return $this->redirectToRoute('order_sub');
//            } else {
//                return $this->redirectToRoute('order_pers');
//            } 
            return new RedirectResponse($request->headers->get('referer'));
//            return $this->redirect($this->generateUrl($request->get('_route'), $request->query->all()));
        }
        return $this->render('offer/new.html.twig', array(
            'offer' => $offer,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a Offer entity.
     *
     */
    public function showAction(Offer $offer)
    {
        $deleteForm = $this->createDeleteForm($offer);
        return $this->render('offer/show.html.twig', array(
            'offer' => $offer,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing Offer entity.
     *
     */
    public function editAction(Request $request, Offer $offer)
    {
        $deleteForm = $this->createDeleteForm($offer);
        $editForm = $this->createForm('AppBundle\Form\OfferType', $offer);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($offer);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Edited Successfully!');
            return $this->redirectToRoute('offer_edit', array('id' => $offer->getId()));
        }
        return $this->render('offer/edit.html.twig', array(
            'offer' => $offer,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a Offer entity.
     *
     */
    public function deleteAction(Request $request, Offer $offer)
    {
    
        $form = $this->createDeleteForm($offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($offer);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The Offer was deleted successfully');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the Offer');
        }
        
        return $this->redirectToRoute('offer');
    }
    
    /**
     * Creates a form to delete a Offer entity.
     *
     * @param Offer $offer The Offer entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Offer $offer)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('offer_delete', array('id' => $offer->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete Offer by id
     *
     */
    public function deleteByIdAction(Offer $offer){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $em->remove($offer);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The Offer was deleted successfully');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the Offer');
        }

        return $this->redirect($this->generateUrl('offer'));

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
                $repository = $em->getRepository('AppBundle:Offer');

                foreach ($ids as $id) {
                    $offer = $repository->find($id);
                    $em->remove($offer);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'offers was deleted successfully!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the offers ');
            }
        }

        return $this->redirect($this->generateUrl('offer'));
    }
    

}
