<?php

namespace Tests\Unit\Services;

use App\Services\EmailService;
use Mockery;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Model\SendSmtpEmail;
use Tests\TestCase;

class EmailServiceTest extends TestCase
{
    private function withMockedApi(EmailService $service, $api): void
    {
        $property = new \ReflectionProperty($service, 'apiInstance');
        $property->setAccessible(true);
        $property->setValue($service, $api);
    }

    public function test_send_email_builds_the_payload_and_calls_the_api(): void
    {
        $service = new EmailService();

        $api = Mockery::mock(TransactionalEmailsApi::class);
        $api->shouldReceive('sendTransacEmail')
            ->once()
            ->with(Mockery::on(function (SendSmtpEmail $email) {
                return $email->getSubject() === 'Bienvenue'
                    && $email->getTo() === [['email' => 'user@example.com']];
            }))
            ->andReturn('sent');

        $this->withMockedApi($service, $api);

        $result = $service->sendEmail([
            'subject' => 'Bienvenue',
            'to' => [['email' => 'user@example.com']],
        ]);

        $this->assertSame('sent', $result);
    }
}
