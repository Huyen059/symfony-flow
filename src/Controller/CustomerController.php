<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CustomerController extends AbstractController
{
    /**
     * @Route("/customer", name="customer")
     */
    public function index()
    {
        return $this->render('customer/show.html.twig', [
            'controller_name' => 'CustomerController',
        ]);
    }

    /**
     * @Route("/customer/tickets", name="customer_tickets")
     */
    public function customerTickets()
    {
        return $this->render('customer/customerTickets.html.twig', [
            'controller_name' => 'CustomerController',
        ]);
    }

}

