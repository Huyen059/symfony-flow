<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Ticket;
use App\Form\CommentType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class CustomerController extends AbstractController
{
    /**
     * @Route("/customer", name="customer_home")
     */
    public function index()
    {
        $tickets = $this->getDoctrine()->getRepository(Ticket::class)->findBy(['customer' => $this->getUser()]);


        return $this->render('customer/index.html.twig', [
            'tickets' => $tickets,
        ]);

    }

    /**
     * @Route("/customer/tickets/{id}", name="customer_tickets")
     */
    public function customerTickets(Ticket $ticket, Request $request, MailerInterface $mailer)
    {
        $style = __DIR__ . "/../../public/assets/customerStyle.css";
        $comment = new Comment();
        $comment->setUser($this->getUser());
        $comment->setTicket($ticket);
        $error = "";

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setUpdatedDate(new \DateTimeImmutable());
            if ($ticket->getStatus() === Ticket::WAITING_FOR_CUSTOMER_FEEDBACK) {
                $ticket->setStatus(Ticket::IN_PROGRESS);

                $email = (new Email())
                    ->from('becode@test.com')
                    ->to('samihuyen059@gmail.com')
                    ->subject('Customer has posted their feedback.')
                    ->text('Customer has posted their feedback. Please review/answer this new feedback.');
                try {
                    $mailer->send($email);
                }
                catch (TransportExceptionInterface $e) {
                    $error = $e->getMessage();
                }

            }

            $this->getDoctrine()->getManager()->persist($comment);
            $this->getDoctrine()->getManager()->persist($ticket);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('customer_tickets', ['id' => $ticket->getId()]);

        }
        if ($request->request->get('reOpen')) {
            /** @var \DateTimeImmutable $update */
            $update = $ticket->getUpdatedDate();
            if ($update->add(new \DateInterval("PT1H")) >= new \DateTimeImmutable() && $ticket->getCloseReason() === null) {
                $ticket->setStatus(Ticket::IN_PROGRESS);
                $ticket->getAgent()->setReopen($ticket->getAgent()->getReopen() + 1);
                $this->getDoctrine()->getManager()->persist($ticket);
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('customer_tickets', ['id' => $ticket->getId()]);
            }
            $error = " You can no longer Re-open a case after it has expired or is permanently closed by manager.";


        }


        return $this->render('customer/customerTickets.html.twig', [
            'form' => $form->createView(),
            'ticket' => $ticket,
            'close' => Ticket::CLOSE,
            'error' => $error,
            'style' => $style
        ]);
    }

}

