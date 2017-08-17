<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\LegalEntity;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use AppBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

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
    
    
    public function reportPaysAction(Request $request, LegalEntity $legalEntity)
    {
        $approved = $request->get('approved');
        $em = $this->getDoctrine()->getManager();
        
        $admin = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
        $data = array();
        $filterForm = $this->createForm('AppBundle\Form\reportLegPaysFilterType', 
            $data,
            array('approved' => $approved) 
            );
        if ($request->get('filter_action') == 'filter') {
            $filterForm->handleRequest($request);

            if ($filterForm->isSubmitted() && $filterForm->isValid()) {

                $data = $filterForm->getData();
                // Для entity getData возвращает имена вместо Id, поэтому:
                if (array_key_exists('leg',$data)) {
                    $data['leg']=$request->get('leg');
                }    
            }
        }
        $legrep = $em->getRepository('AppBundle:LegalEntity');
        $leg = $admin ? (array_key_exists('leg',$data) && $data['leg']!=='' ? ($data['leg'] instanceof \AppBundle\Entity\LegalEntity ? $data['leg']->getId() : intval($data['leg'])) : $legalEntity->getId()) : $legalEntity->getId();
        $pays = $legrep->paysIspLastYearGroupedByMonthWithCurPlan(
            $leg,
            $approved);
        $legName = $legrep->find($leg)->getName();
        // Формирование массива с месяцами
        
        $mns = $approved ? $this->get('monthes')->get13() : $this->get('monthes')->get14();
        $sums = array();
        foreach ($mns as $mn) {
            $sums[$mn] = 0;
            foreach ($pays as $pay) {
                $sums[$mn]+= array_key_exists($mn, $pay) ? $pay[$mn] : 0;
            }
        }
        $mn0 = (new \DateTime())->format('m.y');
        $sums['sred'] = 0;
        foreach ($pays as $pay) {
            $sums['sred']+= ($pay['summa']-$pay[$mn0])/12;
        }
        
        return $this->render('legalentity/report_pays.html.twig', array(
            'legalEntity' => $legalEntity,
            'mns' => $mns,
            'pays' => $pays,
            'sums' => $sums,
            'approved' => $approved,
            'filterForm' => $filterForm->createView(),
            'legName' => $legName,
//            'dmp' => $dmp,
        ));
    }
    
    public function confirmPaysAction(Request $request, LegalEntity $legalEntity)
    {
        $em = $this->getDoctrine()->getManager();
        $repPay = $em->getRepository('AppBundle:OrderPay');
        
        $user = $this->getUser();
        $filterData = array();
        $userRep = $em->getRepository('AppBundle:User');
        $gid = $request->get('gip');
        $gip = FALSE;
        if ($gid) {
            $gip = $userRep->find($gid);
        }    
        if ($gip) {
            $filterData['gip'] = $gip;
        }
        $filterForm = $this->createForm('AppBundle\Form\confirmPaysFilterType', 
            $filterData, 
            array('user' => $user)
            );
        
        if ($request->get('filter_action') == 'filter') {
            $filterForm->handleRequest($request);

            if ($filterForm->isSubmitted() && $filterForm->isValid()) {

                $filterData = $filterForm->getData();
                // Для entity getData возвращает имена вместо Id, поэтому:
                if (array_key_exists('gip', $filterData)) {
                    $filterData['gip']=$request->get('gip');
                }
            }
        }
        $gipId = array_key_exists('gip',$filterData) && $filterData['gip']!=='' ? ($filterData['gip'] instanceof \AppBundle\Entity\User ? $filterData['gip']->getId() : intval($filterData['gip'])) : 0;
        $pays = $em->getRepository('AppBundle:LegalEntity')->IspOrdersWithStatements($legalEntity->getId(), $gipId);
        // Формирование массива с месяцами
        $mns = $this->get('monthes')->get12();
        $mn0 = (new \DateTime())->format('m.y');
        // Массив для заполнения формы
        $data0 = array();
        foreach ($pays as $pay) {
            $fp = $repPay->find($pay['pid'])->getFactPay();
            if ($fp) {
                $data0['factPay'.$pay['pid']] = $fp;
            } else {
                $data0['factPay'.$pay['pid']] = $pay['planPay'];
            }
        }
        ////Делаем форму
        $fb = $this->createFormBuilder($data0);
        foreach ($pays as $pay) {
            $fb->add('factPay'.$pay['pid'], MoneyType::class, array(
                                'label' => '', 
                                'currency' => 'RUB',
                                'required' => false
                            ));
        }
        $form = $fb->getForm();
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $upd_st_cnt = 0;
            foreach ($data as $key => $value) {
                $id = (int)substr($key,7);
                $pay0 = $repPay->find($id);
                
                if ( $value !== $pay0->getFactPay()) {
                    $pay0->setFactPay($value);
                    $pay0->setChargDate(new \DateTime());
                    $em->persist($pay0);
                    //$em->flush();
                    $upd_st_cnt++;
                }    
            }
            if ($upd_st_cnt > 0) {
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'Выполнено '.$upd_st_cnt.' утверждений на выплату');
            } else {
                $this->get('session')->getFlashBag()->add('info', 'Изменений в утверждениях на выплату не произведено.');
            }
            
        }
        
        return $this->render('legalentity/confirm_pays.html.twig', array(
            'legalEntity' => $legalEntity,
            'mns' => $mns,
            'pays' => $pays,
            'filterForm' => $filterForm->createView(),
            'form' => $form->createView(),
        ));
    }
    
    public function confirmFactPaysAction(Request $request, LegalEntity $legalEntity)
    {
        $em = $this->getDoctrine()->getManager();
        $repPay = $em->getRepository('AppBundle:OrderPay');
        $pays = $em->getRepository('AppBundle:LegalEntity')->IspOrdersWithChargs($legalEntity->getId());

        $mn0 = (new \DateTime())->format('m.y');
        // Массив для заполнения формы
        $data0 = array();
        foreach ($pays as $pay) {
            $pd = $repPay->find($pay['pid'])->getPayDate();
            if ($pd) {
                $data0['payDate'.$pay['pid']] = $pd;
            } else {
                $data0['payDate'.$pay['pid']] = new \DateTime();//$pay['chDate'];
            }
            $data0['confirm'.$pay['pid']] = TRUE;
        }
        ////Делаем форму
        $fb = $this->createFormBuilder($data0);
        foreach ($pays as $pay) {
            $fb->add('payDate'.$pay['pid'], DateTimePickerType::class, [
                'label' => 'Дата выплаты',
                'format' => 'dd.MM.yyyy',
                'required' => false
            ]);
            $fb->add('confirm'.$pay['pid'], CheckboxType::class, array(
                    'label' => false,
                    'required' => false,
            ));
        }
        $form = $fb->getForm();
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $upd_st_cnt = 0;
            $not_confirm_cnt=0;
            foreach ($data as $key => $value) {
                if (substr($key,0,7)==='payDate') {
                    $id = (int)substr($key,7);
                    $pay0 = $repPay->find($id);
                    if ($data['confirm'.$id]) {
                        if ( $value !== $pay0->getPayDate()) {
                            $pay0->setPayDate($value);
                            $em->persist($pay0);
                            //$em->flush();
                            $upd_st_cnt++;
                        } 
                    } else {
                        $pay0->setPayDate(NULL);
                        $em->persist($pay0);
                        //$em->flush();
                        $not_confirm_cnt++;
                    }    
                }    
            }
            if ($upd_st_cnt > 0 or $not_confirm_cnt > 0) {
                $em->flush();
                if ($upd_st_cnt > 0) {
                    $this->get('session')->getFlashBag()->add('success', 'Выполнено '.$upd_st_cnt.' подтверждений дат выплат');
                }
                if ($not_confirm_cnt > 0) {
                    $this->get('session')->getFlashBag()->add('warning', 'Не подтверждено '.$not_confirm_cnt.' дат выплат');
                }
            } else {
                $this->get('session')->getFlashBag()->add('info', 'Изменений в датах выплат не произведено.');
            }
            
        }
        
        return $this->render('legalentity/confirm_fact_pays.html.twig', array(
            'legalEntity' => $legalEntity,
            'pays' => $pays,
            'form' => $form->createView(),
        ));
    }
    

}
