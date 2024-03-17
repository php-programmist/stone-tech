<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailerManager
{
    private const RECIPIENTS = [
        'anya-programmist@qmotors.ru',
        '89853148967@mail.ru',
        'info@stone-tech.ru',
        'bespalov@stone-tech.ru',
    ];

    public function __construct(
        private Environment     $twig,
        private MailerInterface $mailer,
    )
    {

    }

    public function sendCalculationMail(array $params):void
    {
        $html = $this->twig->render('mailer/callback/calculation.html.twig', $params);
        $this->sendToAll('Новая заявка на расчет с сайта Stone-tech.ru', $html);
    }

    public function sendConsultationMail(array $params):void
    {
        $html = $this->twig->render('mailer/callback/consultation.html.twig', $params);
        $this->sendToAll('Сообщение с формы Задать вопрос Stone-tech.ru', $html);
    }

    public function sendOrderMail(array $params):void
    {
        $html = $this->twig->render('mailer/callback/order.html.twig', $params);
        $this->sendToAll('Новая заявка с сайта Stone-tech.ru', $html);
    }

    private function sendToAll(string $subject, string $html):void
    {
        foreach (self::RECIPIENTS as $recipient){
            $email = (new Email())
                ->from('robot@stone-tech.ru')
                ->to($recipient)
                ->subject($subject)
                ->html($html);
            $this->mailer->send($email);
        }
    }
}