<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Ticket;
use App\Form\CommentType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CustomerController extends AbstractController
{
    /**
     * @Route("/customer", name="customer_home")
     */
    public function index()
    {

        return $this->render('customer/index.html.twig', [
            'controller_name' => 'CustomerController',
        ]);

    }

    /**
     * @Route("/customer/tickets/{id}", name="customer_tickets")
     */
    public function customerTickets(Ticket $ticket, Request $request)
    {
        $comment = new Comment();
        $comment->setUser($this->getUser());
        $comment->setTicket($ticket);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
           $this->getDoctrine()->getManager()->persist($comment);
           $this->getDoctrine()->getManager()->flush();

        }

        return $this->render('customer/customerTickets.html.twig', [
            'form' => $form->createView()
        ]);
    }

}

