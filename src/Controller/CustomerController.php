<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\CustomersRepository;

class CustomerController extends AbstractController
{
    private $customersRepo;
    
    public function __construct(CustomersRepository $customersRepo)
    {
        $this->customersRepo = $customersRepo;
    }

    #[Route('/customers/{customerId}', name: 'app_customers')]
    public function index($customerId = null): JsonResponse
    {
        $customers = is_null($customerId) ? $this->customersRepo->findCustomers() : $this->customersRepo->findOneBySomeField($customerId);

        $return = [
            'results' => $customers,
            'info' => [
                'results' => !is_null($customerId) ? 1 : count($customers)
            ]
        ];

        return $this->json($return);
    }
}
