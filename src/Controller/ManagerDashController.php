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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ManagerDashController extends AbstractController
{
    const ROLE_AGENT_SECOND_LINE = 'ROLE_AGENT_SECOND_LINE';

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
}
