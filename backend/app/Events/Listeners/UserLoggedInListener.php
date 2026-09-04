<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);


namespace App\Events\Listeners;

use App\Events\Auth\UserLoggedIn;
use App\Events\Interfaces\EventInterface;
use App\Service\Mailer;

class UserLoggedInListener extends EventListener
{
    protected ?string $event = UserLoggedIn::class;
    private readonly Mailer $mailer;

    
    public function __construct(?Mailer $mailer = null)
    {
        $this->mailer = $mailer ?? Mailer::fromEnv();
    }


    public function handle(EventInterface $event): void
    {
        if (!$event instanceof UserLoggedIn) {
            return; // defensive, shouldn't happen given listensTo()
        }

        
        $this->mailer->send(
            toEmail: $event->email,
            toName: $event->name,
            subject: 'Logged In detected!',
            htmlBody: $this->buildEmailHtml($event->name, $event->ipAddress, $event->accessDate)
            // htmlBody: sprintf(
            //     '<p>Hi %s,</p><p>A new access was detected on the following IP Address: %s at: %s.</p>',
            //     htmlspecialchars($event->name),
            //     htmlspecialchars($event->ipAddress),
                
            // ),
        );
    }



    /**
     * Email clients (Gmail, Outlook, etc.) strip <style> blocks and don't
     * load external stylesheets, so Tailwind/Bootstrap classes never
     * actually apply. Instead this uses inline styles chosen to match
     * Tailwind's default design tokens (spacing scale, gray/indigo
     * palette, font stack) so it reads as "Tailwind-styled" while working
     * in real inboxes. Tables are used for layout since flexbox/grid
     * support is inconsistent across email clients.
     */
    private function buildEmailHtml(string $name, string $ipAddress = '' , string $accessDate = ''): string
    {
        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
 
        return <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Logging Detected</title>
        </head>
        <body style="margin:0; padding:0; background-color:#f3f4f6; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f4f6; padding:32px 16px;">
                <tr>
                    <td align="center">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px; background-color:#ffffff; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06); overflow:hidden;">
                            <tr>
                                <td style="background-color:#4f46e5; padding:32px 40px; text-align:center;">
                                    <p style="margin:0; font-size:20px; font-weight:700; color:#ffffff; letter-spacing:-0.025em;">
                                        New Logging Detected 
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:40px;">
                                    <p style="margin:0 0 16px 0; font-size:16px; line-height:24px; color:#111827;">
                                        Hi {$safeName},
                                    </p>
                                    <p style="margin:0 0 24px 0; font-size:16px; line-height:24px; color:#4b5563;">
                                        A new login was detected on your account!
                                    </p>
                                    <table role="presentation" cellpadding="2" cellspacing="2">
                                        <tr>
                                            <td style="border-radius:6px; background-color:#4f46e5;">
                                                <a href="#" style="display:inline-block; padding:12px 24px; font-size:14px; font-weight:600; color:#ffffff; text-decoration:none; border-radius:6px;">
                                                    IP Address: {$ipAddress}
                                                </a>
                                            </td>
                                            <td style="border-radius:6px; background-color:#4f46e5;">
                                                <a href="#" style="display:inline-block; padding:12px 24px; font-size:14px; font-weight:600; color:#ffffff; text-decoration:none; border-radius:6px;">
                                                    Access Date: {$accessDate}
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:24px 40px; background-color:#f9fafb; border-top:1px solid #e5e7eb;">
                                    <p style="margin:0; font-size:12px; line-height:18px; color:#9ca3af; text-align:center;">
                                        You're receiving this email because you created an account with us.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        HTML;
    }
}
