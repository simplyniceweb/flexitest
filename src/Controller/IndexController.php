<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\CustomersRepository;

class IndexController extends AbstractController
{
    private $customersRepo;
    
    public function __construct(CustomersRepository $customersRepo)
    {
        $this->customersRepo = $customersRepo;
    }

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        $customers = $this->customersRepo->findBy([], null, 100);

        return $this->render('customers/index.html.twig', [
            'controller_name' => 'IndexController',
            'customers' => $customers,
        ]);
    }

    #[Route('/customer/{username}', name: 'customer_details')]
    public function customer($username = null): Response
    {
        $customer = $this->customersRepo->findOneBy(['username' => $username]);

        return $this->render('customers/details.html.twig', [
            'controller_name' => 'IndexController',
            'customer' => $customer,
        ]);
    }
}
