<?php

namespace Beholdr\Sendsay;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;

class SendsayTransport extends AbstractTransport
{
    const BASE_URL = 'https://api.sendsay.ru/general/api/v100/json/';

    const GROUP = 'personal';

    public function __toString(): string
    {
        return 'sendsay';
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $this->makeRequest($this->getPayload($email));
    }

    protected function makeRequest(array $payload): void
    {
        if (! $account = config('sendsay.account')) {
            throw new Exception('Please provide sendsay account!');
        }
        if (! $key = config('sendsay.key')) {
            throw new Exception('Please provide sendsay api key!');
        }

        $request = Http::acceptJson()
            ->withOptions($this->getOptions())
            ->withToken('apikey='.$key, 'sendsay')
            ->baseUrl(self::BASE_URL)
            ->throw();

        $request->post($account, $payload);
    }

    protected function getOptions(): array
    {
        $options = [];

        if (config('sendsay.proxy')) {
            $options['proxy'] = config('sendsay.proxy');
        }

        return $options;
    }

    protected function getPayload(Email $email): array
    {
        $payload = [
            'action' => 'issue.send',
            'sendwhen' => 'now',
            'email' => collect($email->getTo())->first()->getAddress(),
            'to.name' => collect($email->getTo())->first()->getName(),
            'group' => self::GROUP,

            'letter' => [
                'subject' => $email->getSubject(),
                'from.email' => collect($email->getFrom())->first()->getAddress(),
                'from.name' => collect($email->getFrom())->first()->getName(),
                'message' => [],
            ],
        ];

        if ($textBody = $email->getTextBody()) {
            $payload['letter']['message']['text'] = $this->processLink($textBody);
        }
        if ($htmlBody = $email->getHtmlBody()) {
            $payload['letter']['message']['html'] = $this->processLink($htmlBody);
        }

        $attachments = $email->getAttachments();
        if (count($attachments) > 0) {
            $payload['letter']['attaches'] = [];
            foreach ($attachments as $attachment) {
                $payload['letter']['attaches'][] = [
                    'name' => $attachment->getName(),
                    'content' => base64_encode($attachment->getBody()),
                    'encoding' => 'base64',
                ];
            }
            $data['attachments'] = $attachments;
        }

        return $payload;
    }

    protected function processLink(string $message): string
    {
        return Str::replace('#UNSUBSCRIBE_LINK#', '[% param.url_unsub %]', $message);
    }
}
