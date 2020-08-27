<?php

namespace App\Controller;

use App\Entity\Agent;
use App\Entity\Customer;
use App\Entity\Ticket;
use App\Form\AgentType;
use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ManagerDashController extends AbstractController
{
    public const ROLE_AGENT_SECOND_LINE = 'ROLE_AGENT_SECOND_LINE';
    public const ROLE_AGENT = 'ROLE_AGENT';

    /**
     * @Route("/manager/dash", name="manager_dash", methods={"GET", "POST"})
     */
    public function index(Request $request)
    {
        // If convert a customer to agent
        if($request->request->get('convertToAgent')) {
            /** @var Customer $customer */
            $customer = $this->getDoctrine()->getRepository(Customer::class)->findOneBy(['id' => $request->request->get('convertToAgent')]);
            $agent = new Agent();
            $agent->setEmail($customer->getEmail())
                ->setRoles([self::ROLE_AGENT])
                ->setPassword($customer->getPassword());
            // If convert to a second line agent
            if($request->request->get('isSecondLine')) {
                $agent->setRoles([self::ROLE_AGENT_SECOND_LINE])
                    ->setIsSecondLine(true);
            }
            // First remove the customer in database
            $this->getDoctrine()->getManager()->remove($customer);
            $this->getDoctrine()->getManager()->flush();
            // Then add new agent with all data from the removed customer
            $this->getDoctrine()->getManager()->persist($agent);
            $this->getDoctrine()->getManager()->flush();
        }

        $tickets = $this->getDoctrine()
            ->getRepository(Ticket::class)
            ->findAll();
        $agents  = $this->getDoctrine()
            ->getRepository(Agent::class)
            ->findAll();
        $customers  = $this->getDoctrine()
            ->getRepository(Customer::class)
            ->findAll();
        return $this->render('manager_dash/index.html.twig', [
            'controller_name' => 'Manager',
            'agents' => $agents,
            'tickets' => $tickets,
            'customers' => $customers
        ]);
    }

    /**
     * @Route("/manager/new-agent", name="agent_new", methods={"GET","POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */

    public function newAgent(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $agent = new Agent();
        $form = $this->createForm(AgentType::class, $agent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $agent->setPassword(
                $passwordEncoder->encodePassword(
                    $agent,
                    $form->get('plainPassword')->getData()
                )
            );
            // Check if agent is second line
            if($form->get('isSecondLine')){
                $agent->setIsSecondLine(true);
                $agent->setRoles([self::ROLE_AGENT_SECOND_LINE]);
            }
            // Save this ticket to database
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($agent);
            $entityManager->flush();

            return $this->redirectToRoute('manager_dash');
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
            if ($ticket->getStatus() !== Ticket::CLOSE)
            {
                $ticket->setStatus(Ticket::OPEN);
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
