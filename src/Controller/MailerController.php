<?php

namespace App\Controller;

use App\Service\MailerManager;
use App\Service\TelegramApiManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;


class MailerController extends AbstractController
{
    public function __construct(
        private MailerManager      $mailerManager,
        private TelegramApiManager $telegramApiManager,
    )
    {
    }

    private const BLACK_LIST = [
        '8 (777) 777-7777'
    ];

    /**
     * @Route("/raschet_form", name="raschet_form")
     */
    public function raschet_form(Request $request): JsonResponse
    {
        $response = new JsonResponse(['success' => '<p>Спасибо! Ваша заявка отправлена.</p>']);
        if (in_array($request->get('form-phone'), self::BLACK_LIST, true)) {
            return $response;
        }
        $params = [
            'name' => $request->get('client-name'),
            'phone' => $request->get('form-phone'),
            'url' => $request->get('client-name'),
            'pageName' => $request->get('title'),
        ];
        $this->mailerManager->sendCalculationMail($params);
        $this->telegramApiManager->sendAdminMessage('telegram/callback/calculation.html.twig', $params);

        return $response;

    }


    /**
     * @Route("/application", name="application_form")
     */
    public function application(Request $request, MailerInterface $mailer)
    {
        $response = new JsonResponse(['success' => '<p>Спасибо! Ваше сообщение отправлено.</p>']);
        if (in_array($request->get('telephone'), self::BLACK_LIST, true)) {
            return $response;
        }
        $params = [
            'name' => $request->get('name'),
            'phone' => $request->get('telephone'),
        ];
        $this->mailerManager->sendConsultationMail($params);
        $this->telegramApiManager->sendAdminMessage('telegram/callback/consultation.html.twig', $params);

        return new JsonResponse(['success'=>'<p>Спасибо! Ваше сообщение отправлено.</p>']);
    }

    /**
     * @Route("/callback_form", name="callback_form")
     */
    public function callback_form(Request $request)
    {
        $response = new JsonResponse(['success' => '<p>Спасибо! Ваша заявка отправлена.</p>']);
        if (in_array($request->get('form-phone'), self::BLACK_LIST, true)) {
            return $response;
        }

        $productType = $this->getProduct($request->get('form-product'));
        $params = [
            'name' => $request->get('client-name'),
            'phone' => $request->get('form-phone'),
            'productType' => $request->get($productType),
        ];

        $this->mailerManager->sendOrderMail($params);
        $this->telegramApiManager->sendAdminMessage('telegram/callback/order.html.twig', $params);

        return new JsonResponse(['success'=>'<p>Спасибо! Ваша заявка отправлена.</p>']);
    }

    public function getProduct($product){
        return match ($product) {
            1 => 'Подоконники',
            2 => 'Столешницы',
            3 => 'Ступени',
            4 => 'Лестницы',
            5 => 'Камины',
            default => 'Изделие не выбрано',
        };
    }

}
