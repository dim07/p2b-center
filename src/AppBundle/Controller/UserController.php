<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User controller.
 *
 */
class UserController extends Controller
{
    /**
     * Lists all User entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:User')->createQueryBuilder('e');
        
        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($users, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        return $this->render('user/index.html.twig', array(
            'users' => $users,
            'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView(),

        ));
    }
    
    /**
     * Lists all experts.
     *
     */
    public function expertsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:User')->createQueryBuilder('u')
                        ->leftJoin('u.legentity','l')
                        ->where('l.id is NULL OR ((l.customer IS NULL OR l.customer = 0) AND (l.contractor IS NULL OR l.contractor = 0))')    
                        ->orderBy('u.rating, u.fio', 'ASC');
        
        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($users, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        return $this->render('user/experts.html.twig', array(
            'users' => $users,
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
        $filterForm = $this->createForm('AppBundle\Form\UserFilterType');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('UserControllerFilter');
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
                $session->set('UserControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
//            if ($session->has('UserControllerFilter')) {
//                $filterData = $session->get('UserControllerFilter');
//                
//                $em = $this->getDoctrine()->getManager();
//                
//                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
//                    if (is_object($filter) && $key !== 'sections') {
//                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
//                    }
//                    if ($key == 'sections') {
//                        $secs =  new ArrayCollection();
//                        foreach ($filterData['sections'] as $sec) {
//                            $secs[] = $em->getRepository('AppBundle:User')->findOneById($sec->getId());
//                        }
//                        $filterData['sections'] = $secs;
//                        
//                    }
//                }
//                
//
////                $filterForm = $this->createForm('AppBundle\Form\UserFilterType', $filterData);
////                 $filterForm->setData($filterData);
//                
//                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
//            }
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
            return $me->generateUrl('experts', $requestParams);
        };

        // Paginator - view
        $view = new TwitterBootstrap3View();
        $pagerHtml = $view->render($pagerfanta, $routeGenerator, array(
            'proximity' => 5,
            'prev_message' => '<i class = "fa fa-arrow-left"></i>',
            'next_message' => '<i class = "fa fa-arrow-right"></i>',
        ));

        return array($entities, $pagerHtml);
    }
    
    

    /**
     * Displays a form to create a new User entity.
     *
     */
    public function newAction(Request $request)
    {
    
        $user = new User();
        $form   = $this->createForm('AppBundle\Form\UserType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            $editLink = $this->generateUrl('user_edit', array('id' => $user->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>New user was created successfully.</a>" );
            
            $nextAction=  $request->get('submit') == 'save' ? 'user' : 'user_new';
            return $this->redirectToRoute($nextAction);
        }
        return $this->render('user/new.html.twig', array(
            'user' => $user,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a User entity.
     *
     */
    public function showAction(User $user)
    {
        $deleteForm = $this->createDeleteForm($user);
        return $this->render('user/show.html.twig', array(
            'user' => $user,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing User entity.
     *
     */
    public function editAction(Request $request, User $user)
    {
        $deleteForm = $this->createDeleteForm($user);
        $editForm = $this->createForm('AppBundle\Form\UserType', $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Edited Successfully!');
            return $this->redirectToRoute('user_edit', array('id' => $user->getId()));
        }
        return $this->render('user/edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a User entity.
     *
     */
    public function deleteAction(Request $request, User $user)
    {
    
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The User was deleted successfully');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the User');
        }
        
        return $this->redirectToRoute('user');
    }
    
    /**
     * Creates a form to delete a User entity.
     *
     * @param User $user The User entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $user->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete User by id
     *
     */
    public function deleteByIdAction(User $user){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $em->remove($user);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The User was deleted successfully');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the User');
        }

        return $this->redirect($this->generateUrl('user'));

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
                $repository = $em->getRepository('AppBundle:User');

                foreach ($ids as $id) {
                    $user = $repository->find($id);
                    $em->remove($user);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'users was deleted successfully!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the users ');
            }
        }

        return $this->redirect($this->generateUrl('user'));
    }
    

}
