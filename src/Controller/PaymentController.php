<?php

namespace App\Controller;

use Stripe\StripeClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{
    #[Route('/payment', name: 'app_payment')]
    public function index(): Response
    {
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }


    #[Route('/checkout', name: 'checkout')]
    public function checkout($stripeSK): RedirectResponse
    {
        $stripe = new StripeClient($stripeSK);

        $checkoutSession = $stripe->checkout->sessions->create([
            'line_items'  => [
                [
                    'price_data' => [
                        'currency'     => 'usd',
                        'product_data' => [
                            'name' => 'T-shirt',
                        ],
                        'unit_amount'  => 2000,
                    ],
                    'quantity'   => 1,
                ],
            ],
            'mode'        => 'payment',
            'success_url' => $this->generateUrl('success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url'  => $this->generateUrl('cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($checkoutSession->url, 303);
    }

    #[Route('/success', name: 'success')]
    public function success(): Response
    {
        return $this->render('payment/success.html.twig', []);
    }

    #[Route('/cancel', name: 'cancel')]
    public function cancel(): Response
    {
        return $this->render('payment/cancel.html.twig', []);
    }
}
