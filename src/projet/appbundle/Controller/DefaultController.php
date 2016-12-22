<?php

namespace Projet\AppBundle\Controller;

use Projet\AppBundle\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $usr= $this->get('security.context')->getToken()->getUser();
        $id = $usr->getId();

        $contacts = $em->getRepository('ProjetAppBundle:Contact')->findByIdUser($id);

        return $this->render('ProjetAppBundle:Default:index.html.twig', array(
            'contacts' => $contacts,
        ));
    }

    public function newAction(Request $request)
    {
        $contact = new Contact();
        $form = $this->createForm('Projet\AppBundle\Form\ContactType', $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $usr= $this->get('security.context')->getToken()->getUser();
            $id = $usr->getId();
            $contact->setIdUser($id);
            $em->persist($contact);
            $em->flush($contact);

            return $this->redirectToRoute('contact_show', array('id' => $contact->getId()));
        }

        return $this->render('ProjetAppBundle:Default:new.html.twig', array(
            'contact' => $contact,
            'form' => $form->createView(),
        ));
    }


    public function showAction(Contact $contact)
    {
        $deleteForm = $this->createDeleteForm($contact);

        return $this->render('ProjetAppBundle:Default:show.html.twig', array(
            'contact' => $contact,
            'delete_form' => $deleteForm->createView(),
        ));
    }


    public function editAction(Request $request, Contact $contact)
    {
        $deleteForm = $this->createDeleteForm($contact);
        $editForm = $this->createForm('Projet\AppBundle\Form\ContactType', $contact);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('contact_edit', array('id' => $contact->getId()));
        }

        return $this->render('ProjetAppBundle:Default:edit.html.twig', array(
            'contact' => $contact,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    public function deleteAction(Request $request, Contact $contact)
    {
        $form = $this->createDeleteForm($contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($contact);
            $em->flush($contact);
        }

        return $this->redirectToRoute('contact_index');
    }


    private function createDeleteForm(Contact $contact)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('contact_delete', array('id' => $contact->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
