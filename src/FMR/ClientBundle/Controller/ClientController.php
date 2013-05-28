<?php

namespace FMR\ClientBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FMR\ClientBundle\Entity\Client;
use FMR\ClientBundle\Form\ClientType;


/**
 * Client controller.
 *
 * @Route("/client")
 */
class ClientController extends Controller
{
    /**
     * Lists all Client entities.
     *
     * @Route("/", name="client")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FMRClientBundle:Client')->findRecents();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * search and displays a Client entity.
     *
     * @Route("/search/", name="client_search")
     * @Template("FMRClientBundle:Client:index.html.twig")
     */
    public function searchAction(Request $request)
    {
    	
    	$q = $request->get('q');
    	
    	$em = $this->getDoctrine()->getManager();
    
    	$entities = $em->getRepository('FMRClientBundle:Client')->search($q);
    	$count = count($entities);
    	
    	return array(
    			'entities' => $entities,
    			'recherche' => $q,
    			'count' => $count,
    	);
    }
    
    /**
     * Creates a new Client entity.
     *
     * @Route("/", name="client_create")
     * @Method("POST")
     * @Template("FMRClientBundle:Client:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Client();
        $form = $this->createForm(new ClientType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            
			$this->get('session')->getFlashBag()->add('success', 'Cr&eacute;ation compl&egrave;te');
		
            return $this->redirect($this->generateUrl('client_show', array('id' => $entity->getId())));
        }
		$this->get('session')->getFlashBag()->add('error', 'Erreur lors de la cr&eacute;ation');
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Client entity.
     *
     * @Route("/new", name="client_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Client();
        $form   = $this->createForm(new ClientType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Client entity.
     *
     * @Route("/{id}", name="client_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMRClientBundle:Client')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Client entity.');
        }

        
        $em = $this->getDoctrine()->getManager();
        
        //Selection des offres en cours
        $qb = $em->createQueryBuilder();
        $qb
        ->select('o')
        ->from('FMROffreBundle:Offre', 'o')
        ->innerJoin('o.client', 'c')
        ->innerJoin('o.statut', 's')
        ->where('c.id = ?1')
        ->andWhere('s.id = 1 or s.id = 2')
        ->setParameter('1',$id)
        ;
        $query = $qb->getQuery();
        $offres = $query->getResult();
        
        //Selection des chantiers en cours
        $qb = $em->createQueryBuilder();
        $qb
        ->select('h')
    	->from('FMRChantierBundle:Chantier', 'h')
        ->innerJoin('h.client', 'c')
        ->where('c.id = ?1')
        ->andWhere('(h.dateFin >= ?2 OR h.dateFin is null)')
        ->setParameter('1',$id)
        ->setParameter('2', new \DateTime())
        ;
        $query = $qb->getQuery();
        $chantiers = $query->getResult();
        
        //Selection des offres en cours
        $qb = $em->createQueryBuilder();
        $qb
	        ->select('f')
	        ->from('FMRFactureBundle:Facture', 'f')
	        ->innerJoin('f.client', 'c')
	        ->innerJoin('f.statut', 's')
	        ->where('c.id = ?1')
	        ->andWhere('s.id = 1 or s.id = 2')
	        ->setParameter('1',$id)
        ;
        $query = $qb->getQuery();
        $factures = $query->getResult();
        
        return array(
            'entity'      => $entity,
        	'offres' =>  $offres,
        	'chantiers' => $chantiers,
        	'factures' => $factures,
        );
    }

    /**
     * Displays a form to edit an existing Client entity.
     *
     * @Route("/{id}/edit", name="client_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMRClientBundle:Client')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Client entity.');
        }

        $editForm = $this->createForm(new ClientType(), $entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Client entity.
     *
     * @Route("/{id}", name="client_update")
     * @Method("PUT")
     * @Template("FMRClientBundle:Client:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FMRClientBundle:Client')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Client entity.');
        }

        $editForm = $this->createForm(new ClientType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Modification r&eacute;ussie');

            return $this->redirect($this->generateUrl('client_edit', array('id' => $id)));
        }
        
		$this->get('session')->getFlashBag()->add('error', 'Erreur lors de la modification');
        
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Deletes a Client entity.
     *
     * @Route("/{id}/delete", name="client_delete")
     *@Method("GET")
     */
    public function deleteAction($id)
    {
    	$em = $this->getDoctrine()->getManager();
    	 
		$entity = $em->getRepository('FMRClientBundle:Client')->find($id);
		
		if (!$entity) {
			throw $this->createNotFoundException('Unable to find Client entity.');
		}

		$em->remove($entity);
		$em->flush();
		
		$this->get('session')->getFlashBag()->add('success', 'Supression accomplie !');

        return $this->redirect($this->generateUrl('client'));
    }
}
