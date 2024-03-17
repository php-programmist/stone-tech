<?php

namespace App\Controller;

use App\Service\TelegramApiManager;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class TelegramWebhookController extends AbstractController
{
    #[Route(
        path: '/telegram/webhook/{token}',
        name: 'webhook_telegram',
    )]
    public function index(
        string $token,
        TelegramApiManager $manager,
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $response = new Response();
        $manager->verifyWebhookToken($token);
        try{
            $manager->handleStart(json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR));
        } catch (Throwable $e){
            $logger->error('TELEGRAM WEBHOOK ERROR: ' . $e->getMessage());
            $response->setStatusCode(500)
                     ->setContent($e->getMessage());
        }
        
        return $response;
    }
}
