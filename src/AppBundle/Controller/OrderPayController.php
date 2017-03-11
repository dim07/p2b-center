<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\OrderPay;

/**
 * OrderPay controller.
 *
 */
class OrderPayController extends Controller
{
    /**
     * Lists all OrderPay entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:OrderPay')->createQueryBuilder('e');
        
        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($orderPays, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        return $this->render('orderpay/index.html.twig', array(
            'orderPays' => $orderPays,
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
        $filterForm = $this->createForm('AppBundle\Form\OrderPayFilterType');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('OrderPayControllerFilter');
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
                $session->set('OrderPayControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('OrderPayControllerFilter')) {
                $filterData = $session->get('OrderPayControllerFilter');
                
                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
                    if (is_object($filter)) {
                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
                    }
                }
                
                $filterForm = $this->createForm('AppBundle\Form\OrderPayFilterType', $filterData);
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
            return $me->generateUrl('orderpay', $requestParams);
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
     * Displays a form to create a new OrderPay entity.
     *
     */
    public function newAction(Request $request)
    {
    
        $orderPay = new OrderPay();
        $form   = $this->createForm('AppBundle\Form\OrderPayType', $orderPay);
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
            $orderPay->setOrder($order);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($orderPay);
            $em->flush();
            
            $editLink = $this->generateUrl('orderpay_edit', array('id' => $orderPay->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>Новая выплата успешно создана.</a>" );
            
//            $nextAction=  $request->get('submit') == 'save' ? 'orderpay' : 'orderpay_new';
//            return $this->redirectToRoute($nextAction);
            return $this->redirectToRoute('stageorder_edit',  array('id' => $order_id));
        }
        return $this->render('orderpay/new.html.twig', array(
            'orderPay' => $orderPay,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a OrderPay entity.
     *
     */
    public function showAction(OrderPay $orderPay)
    {
        $deleteForm = $this->createDeleteForm($orderPay);
        return $this->render('orderpay/show.html.twig', array(
            'orderPay' => $orderPay,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing OrderPay entity.
     *
     */
    public function editAction(Request $request, OrderPay $orderPay)
    {
        $deleteForm = $this->createDeleteForm($orderPay);
        $editForm = $this->createForm('AppBundle\Form\OrderPayType', $orderPay);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($orderPay);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Edited Successfully!');
            return $this->redirectToRoute('orderpay_edit', array('id' => $orderPay->getId()));
        }
        return $this->render('orderpay/edit.html.twig', array(
            'orderPay' => $orderPay,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a OrderPay entity.
     *
     */
    public function deleteAction(Request $request, OrderPay $orderPay)
    {
    
        $form = $this->createDeleteForm($orderPay);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($orderPay);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The OrderPay was deleted successfully');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the OrderPay');
        }
        
        return $this->redirectToRoute('orderpay');
    }
    
    /**
     * Creates a form to delete a OrderPay entity.
     *
     * @param OrderPay $orderPay The OrderPay entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(OrderPay $orderPay)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('orderpay_delete', array('id' => $orderPay->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete OrderPay by id
     *
     */
    public function deleteByIdAction(OrderPay $orderPay){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $em->remove($orderPay);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The OrderPay was deleted successfully');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the OrderPay');
        }

        return $this->redirect($this->generateUrl('orderpay'));

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
                $repository = $em->getRepository('AppBundle:OrderPay');

                foreach ($ids as $id) {
                    $orderPay = $repository->find($id);
                    $em->remove($orderPay);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'orderPays was deleted successfully!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the orderPays ');
            }
        }

        return $this->redirect($this->generateUrl('orderpay'));
    }
    

}
