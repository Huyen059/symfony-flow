<?php

namespace App\Controller;

use App\Entity\Agent;
use App\Entity\Ticket;
use App\Form\AgentType;
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

    /**
     * @Route("/manager/new-agent", name="agent_new", methods={"GET","POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */

    public function newAgent(Request $request)
    {
        $agent = new Agent();
        $form = $this->createForm(AgentType::class, $agent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Save this ticket to database
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($agent);
            $entityManager->flush();

            return $this->redirectToRoute('ticket_index');
        }

        return $this->render('agent/register.html.twig', [
            'agent' => $agent,
            'agentForm' => $form->createView(),
        ]);
    }
    /**
     * @Route("/manager/ticket_reset", name="ticket_reset", methods={"GET"})
     */

    public function endOfDayReset()
    {
        $tickets = $this->getDoctrine()
            ->getRepository(Ticket::class)
            ->findAll();

        foreach($tickets as $ticket)
        {
            if ($ticket->getStatus() !== 3)
            {
                $ticket->setStatus(0);
                $ticket->removeAgent();
            }
        }
        $agents  = $this->getDoctrine()
            ->getRepository(Agent::class)
            ->findAll();
        return $this->render('manager_dash/index.html.twig', [
            'controller_name' => 'Manager',
            'agents' => $agents,'tickets' => $tickets]);

    }
}
