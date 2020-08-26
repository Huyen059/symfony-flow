<?php

namespace App\Controller;

use App\Entity\Agent;
use App\Entity\Customer;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ManagerDashController extends AbstractController
{
    /**
     * @Route("/manager/dash", name="manager_dash", methods={"GET"})
     * @param UserRepository $userRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $customers = $this->getDoctrine()
            ->getRepository(Customer::class)
            ->findAll();
        $agents  = $this->getDoctrine()
            ->getRepository(Agent::class)
            ->findAll();

        return $this->render('manager_dash/index.html.twig', [
            'controller_name' => 'Manager',
            'customers' => $customers,
            'agents' => $agents
        ]);
    }
}
