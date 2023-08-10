<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Message;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class MailerController extends AbstractController
{
    private const BLACK_LIST = [
        '8 (777) 777-7777'
    ];

    /**
     * @Route("/raschet_form", name="raschet_form")
     */
    public function raschet_form(Request $request, MailerInterface $mailer)
    {
        $response = new JsonResponse(['success' => '<p>Спасибо! Ваша заявка отправлена.</p>']);
        if (in_array($request->get('form-phone'), self::BLACK_LIST, true)) {
            return $response;
        }

        $to = array('anya-programmist@qmotors.ru', '89853148967@mail.ru', 'info@stone-tech.ru', 'bespalov@stone-tech.ru');
        foreach ($to as $recipient){
            $email = (new Email())
                ->from('robot@stone-tech.ru')
                ->to((string)$recipient)
                ->subject('Новая заявка на расчет с сайта Stone-tech.ru')
                ->html('<p>Новая заявка на расчет с сайта Stone-tech.ru</p>
             <p>Имя отправителя: ' . $request->get('client-name') . '</p>
            <p>Телефон отправителя: ' . $request->get('form-phone') . '</p>
            <p>URL страницы: ' . $request->get('url') . '</p>
            <p>Название страницы: ' . $request->get('title') . '</p>
            '
                );
            $mailer->send($email);
        }

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
        // $to = explode(',',$this->getTo($request->get('salon')) );
        $to = array('anya-programmist@qmotors.ru', '89853148967@mail.ru', 'info@stone-tech.ru', 'bespalov@stone-tech.ru');

            $userName = $request->get('name');
            $userPhone = $request->get('telephone');


            foreach ($to as $recipient){
                $email = (new Email())
                    ->from('robot@stone-tech.ru')
                    ->to($recipient)
                    ->subject('Сообщение с формы Задать вопрос Stone-tech.ru')
                    ->html('<p>Сообщение с формы Задать вопрос Stone-tech.ru:</p>
                     <p>Имя отправителя: ' . $userName . '</p>
                    <p>Телефон отправителя: ' . $userPhone . '</p>'
                    );
                $mailer->send($email);
            }


            return new JsonResponse(['success'=>'<p>Спасибо! Ваше сообщение отправлено.</p>']);


    }

    /**
     * @Route("/callback_form", name="callback_form")
     */
    public function callback_form(Request $request, MailerInterface $mailer)
    {
        $response = new JsonResponse(['success' => '<p>Спасибо! Ваша заявка отправлена.</p>']);
        if (in_array($request->get('form-phone'), self::BLACK_LIST, true)) {
            return $response;
        }
        //$to = explode(',',$this->getTo($request->get('salon')) );
        $to = array('anya-programmist@qmotors.ru', '89853148967@mail.ru', 'info@stone-tech.ru', 'bespalov@stone-tech.ru');
        $productType = $this->getProduct($request->get('form-product'));
        foreach ($to as $recipient){
            $email = (new Email())
                ->from('robot@stone-tech.ru')
                ->to((string)$recipient)
                ->subject('Новая заявка с сайта Stone-tech.ru')
                ->html('<p>Новая заявка с сайта Stone-tech.ru</p>
             <p>Имя отправителя: ' . $request->get('client-name') . '</p>
            <p>Телефон отправителя: ' . $request->get('form-phone') . '</p>
            <p>Выбранное изделие:' .$productType.' </p>
            '
                );
            $mailer->send($email);
        }

        return new JsonResponse(['success'=>'<p>Спасибо! Ваша заявка отправлена.</p>']);
    }

    public function addEmail($email, ValidatorInterface $validator){
        $emailConstraint = array(
            new Assert\Email(),
            new Assert\NotBlank(),
        );
        $errors = $validator->validate(
            $email,
            $emailConstraint
        );

        if(0 === count($errors)){
            return true;
        }else{
            return false;
        }
    }

    public function addName($name, ValidatorInterface $validator){
        $nameConstraint = array(
            new Assert\NotBlank(),
            new Assert\Length(['min' => 2]),
            new Assert\Regex(['pattern' => '/^[а-яёА-ЯЁ]+$/'])
        );

        $errors = $validator->validate(
            $name,
            $nameConstraint
        );
        if(0 === count($errors)){
            return true;
        }else{
            return false;
        }
    }

    public function addPhone($phone, ValidatorInterface $validator){
        $phoneConstraint = array(
            new Assert\NotBlank(),
            new Assert\Regex(['pattern' => '/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/'])
        );
        $errors = $validator->validate(
            $phone,
            $phoneConstraint
        );
        if(0 === count($errors)){
            return true;
        }else{
            return false;
        }
    }


    public function getProduct($product){
        switch ($product){
            case 1:
                return 'Подоконники';
            case 2:
                return 'Столешницы';
            case 3:
                return 'Ступени';
            case 4:
                return 'Лестницы';
            case 5:
                return 'Камины';
            default:
                return 'Изделие не выбрано';
        }
    }

}
