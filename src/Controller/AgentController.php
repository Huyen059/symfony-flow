<?php

namespace App\Controller;

use App\Entity\Agent;
use App\Entity\Comment;
use App\Entity\Ticket;
use App\Form\AgentCommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AgentController extends AbstractController
{
    const ROLE_AGENT_SECOND_LINE = 'ROLE_AGENT_SECOND_LINE';

    /**
     * @Route("/agent/home", name="agent_home", methods={"GET", "POST"})
     */
    public function index(Request $request)
    {
        /**
         * @var Agent $agent
         */
        $agent = $this->getUser();
        if ($agent->getRoles()[0] === self::ROLE_AGENT_SECOND_LINE) {
            $agent->setIsSecondLine(true);
        }

        // Assign the chosen ticket to this agent
        if ($request->request->get('addToDo')) {
            $ticket = $this->getDoctrine()->getRepository(Ticket::class)->findOneBy(['id' => $request->request->get('addToDo')]);
            $ticket->setAgent($agent)
                ->setStatus(Ticket::IN_PROGRESS)
                ->setUpdatedDate(new \DateTimeImmutable());
            $this->getDoctrine()->getManager()->persist($ticket);
            $this->getDoctrine()->getManager()->flush();
        }

        // Get the tickets that match the agent (normal agent: not-escalated ticket, second-line agent: escalated ticket)
        $tickets = $this->getDoctrine()->getRepository(Ticket::class)->findBy(['isEscalated' => $agent->getIsSecondLine()]);

        return $this->render('agent/index.html.twig', [
            'tickets' => $tickets,
            'agent' => $agent,
            'open' => Ticket::OPEN,
            'close' => Ticket::CLOSE,
        ]);
    }

    /**
     * @Route("/agent/ticket/{id}", name="agent_ticket", methods={"GET", "POST"})
     */
    public function addComment(Ticket $ticket, Request $request)
    {
        $error = '';
        /**
         * @var Agent $agent
         */
        $agent = $this->getUser();
        if ($agent->getRoles()[0] === self::ROLE_AGENT_SECOND_LINE) {
            $agent->setIsSecondLine(true);
        }
        $comment = new Comment();
        $form = $this->createForm(AgentCommentType::class, $comment);
        $form->handleRequest($request);

        // If the add comment form is submitted
        if ($form->isSubmitted() && $form->isValid()) {
            // Assign user and ticket to this comment
            $comment->setUser($agent)
                ->setTicket($ticket);
            // Check if this comment is private, if not, change status of ticket
            $isPrivate = $form->get('isPrivate')->getData();
            $comment->setIsPrivate($isPrivate);
            if (!$isPrivate) {
                $ticket->setStatus(Ticket::WAITING_FOR_CUSTOMER_FEEDBACK)
                    ->setUpdatedDate(new \DateTimeImmutable());
                $this->getDoctrine()->getManager()->persist($ticket);
            }

            $this->getDoctrine()->getManager()->persist($comment);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('agent_ticket', ['id' => $ticket->getId()]);
        }

        // If the agent closes the ticket
        if ($request->request->get('closeTicket')) {
            foreach ($ticket->getComments() as $comment) {
                if ($comment->getUser() instanceof Agent && !$comment->getIsPrivate()) {
                    $ticket->setStatus(Ticket::CLOSE)
                        ->setUpdatedDate(new \DateTimeImmutable());
                    $this->getDoctrine()->getManager()->persist($ticket);
                    $this->getDoctrine()->getManager()->flush();
                    return $this->redirectToRoute('agent_ticket', ['id' => $ticket->getId()]);
                }
            }

            $error = "A ticket can only be closed when it has at least one public comment from an agent.";
        }

        // If the agent escalates the ticket
        if ($request->request->get('escalateTicket')) {
            $ticket->setIsEscalated(true)
                ->setAgent(null)
                ->setStatus(Ticket::OPEN)
                ->setUpdatedDate(new \DateTimeImmutable());
            $this->getDoctrine()->getManager()->persist($ticket);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('agent_home');
        }

        return $this->render('agent/detail.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
            'close' => Ticket::CLOSE,
            'error' => $error,
            'agent' => $agent
        ]);
    }
}
