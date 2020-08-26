<?php

namespace App\Controller;

use App\Entity\Agent;
use App\Entity\Ticket;
use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ManagerDashController extends AbstractController
{
    /**
     * @Route("/manager/dash", name="manager_dash", methods={"GET"})
     */
    public function index()
    {
        $tickets = $this->getDoctrine()
            ->getRepository(Ticket::class)
            ->findAll();
        $agents  = $this->getDoctrine()
            ->getRepository(Agent::class)
            ->findAll();
        return $this->render('manager_dash/index.html.twig', [
            'controller_name' => 'Manager',
            'agents' => $agents,'tickets' => $tickets]);
    }

}
