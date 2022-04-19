<?php

namespace App\Service;

interface MailServiceInterface
{
    /**
     * @param string $to
     * @param string $subject
     * @param string $htmlTemplate
     * @param array $context
     * @return void
     */
    public function send(string $to, string $subject, string $htmlTemplate, array $context): void;
}
