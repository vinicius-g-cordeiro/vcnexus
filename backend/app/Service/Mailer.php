<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Service;

use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Thin wrapper around PHPMailer so listeners don't need to know
 * SMTP config details - just call send().
 *
 * Config is pulled from environment variables; adjust to whatever
 * config mechanism the rest of the app already uses (e.g. a Config
 * class), this is just the minimal version.
 */
class Mailer
{
    public function __construct(
        private string $host = '',
        private int $port = 587,
        private string $username = '',
        private string $password = '',
        private string $encryption = PHPMailer::ENCRYPTION_STARTTLS,
        private string $fromAddress = '',
        private string $fromName = '',
    ) {
        
    }

    public static function fromEnv(): self
    {
        
        return new self(
            host: getenv('STMP_HOST') ?: 'smtp.gmail.com',
            port: (int) (getenv('STMP_PORT') ?: 587),
            username: trim(file_get_contents(trim(getenv('SMTP_USERNAME')))) ?: 'vinismtpgo@gmail.com',
            password: trim(file_get_contents(trim(getenv('SMTP_PASSWORD')))) ?: '',
            encryption: getenv('STMP_ENCRYPTION') ?: PHPMailer::ENCRYPTION_STARTTLS,
            fromAddress: getenv('STMP_FROM_ADDRESS') ?: 'vinismtpgo@gmail.com',
            fromName: getenv('SMTP_DISPLAYNAME') ?: 'VCNexus',
        );
    }

    /**
     * @throws PHPMailerException
     */
    public function send(string $toEmail, string $toName, string $subject, string $htmlBody, ?string $textBody = null): void
    {
        $mail = new PHPMailer(true);
        

        $mail->isSMTP();
        $mail->Host       = $this->host;
        $mail->Port       = $this->port;
        $mail->SMTPAuth   = $this->username !== '';
        $mail->Username   = $this->username;
        $mail->Password   = $this->password;
        $mail->SMTPSecure = $this->encryption;

        $mail->setFrom($this->fromAddress, $this->fromName);
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody = $textBody ?? strip_tags($htmlBody);
        $mail->send();
    }
}
