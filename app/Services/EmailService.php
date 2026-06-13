<?php

namespace App\Services;

use SendinBlue\Client\Model\SendSmtpEmail;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;

class EmailService
{
    protected $apiInstance;

    public function __construct()
    {
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', config('api.SENDINBLUE_API_KEY'));
        $this->apiInstance = new TransactionalEmailsApi(new \GuzzleHttp\Client(), $config);
    }

    public function sendEmail(array $emailDetails)
    {
        $email = new SendSmtpEmail($emailDetails);
        return $this->apiInstance->sendTransacEmail($email);
    }
}
