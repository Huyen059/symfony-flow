<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CustomerController extends AbstractController
{
    /**
     * @Route("/customer/tickets", name="customer_home")
     */
    public function CustomerTicketsHomepage()
    {
        return $this->render('customer/CustomerTicketsHomepage.html.twig', [
            'controller_name' => 'CustomerController',
        ]);
    }

    /**
     * @Route("/customer/comments", name="customer_home")
     */
    public function CustomerTicketDetails()
    {
        return $this->render('customer/CustomerTicketDetails.html.twig', [
            'controller_name' => 'CustomerController',
        ]);
    }
}
