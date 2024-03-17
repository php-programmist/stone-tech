<?php

namespace App\Service;

use JsonException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class TelegramApiManager
{
    private const WEBHOOK_TOKEN = 'd7OseZ2pViJL1vCls5rnb95dxHU1OQcR';

    /**
     * @param RouterInterface $router
     * @param Environment $twig
     * @param string $telegramBotToken
     * @param string $adminChatId
     */
    public function __construct(
        private RouterInterface $router,
        private Environment     $twig,
        private string          $telegramBotToken,
        private string          $adminChatId,
        private string          $baseHost,
    )
    {

    }

    /**
     * @throws JsonException
     */
    public function setupWebhook(): string
    {
        $url = 'https://' . $this->baseHost . $this->router->generate('webhook_telegram', [
                'token' => self::WEBHOOK_TOKEN,
            ]);

        $response = $this->sendPostRequest($this->getFullEndpointUrl('/setWebhook'), ['url' => $url]);
        $result = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        return $result['description'];
    }

    /**
     * @param array<string, mixed> $update
     *
     * @throws JsonException
     */
    public function handleStart(array $update): void
    {
        $text = $update['message']['text'] ?? '';

        if ('/start' !== $text) {
            return;
        }

        $chatId = $update['message']['chat']['id'] ?? throw new BadRequestException('Не удалось получить ID чата');

        $this->sendMessage($chatId, sprintf('ID чата: <strong>%s</strong>', $chatId));
    }

    /**
     * @throws JsonException
     */
    public function sendMessage(?string $chatId, string $text): void
    {
        if (null === $chatId) {
            return;
        }


        $this->sendPostRequest($this->getFullEndpointUrl('/sendMessage'), [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ]);
    }

    public function sendAdminMessage(string $templateName, array $data): void
    {
        $text = $this->twig->render($templateName, $data);
        $this->sendMessage($this->adminChatId, $text);
    }

    public function verifyWebhookToken(string $token): void
    {
        if ($token !== self::WEBHOOK_TOKEN) {
            throw new AccessDeniedHttpException();
        }
    }

    private function getFullEndpointUrl(string $relativeUri): string
    {
        $relativeUri = ltrim($relativeUri, '/');

        return sprintf("https://api.telegram.org/bot%s/%s", $this->telegramBotToken, $relativeUri);
    }

    /**
     * @throws JsonException
     */
    private function sendPostRequest($url, array $body): string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body, JSON_THROW_ON_ERROR));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

}