<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Shared;

final class Notification
{
    public const SUCCESS = 'success';
    public const DANGER = 'danger';
    public const WARNING = 'warning';
    public const INFO = 'info';

    public const CUSTOM = 'custom';

    public const Icon = [
        self::SUCCESS => 'bi bi-check-circle-fill',
        self::DANGER => 'bi bi-x-circle-fill',
        self::WARNING => 'bi bi-exclamation-triangle-fill',
        self::INFO => 'bi bi-info-circle-fill',
        self::CUSTOM => 'bi bi-info-circle-fill'
    ];

    public const IconColor = [
        self::SUCCESS => '#28a745',
        self::DANGER => '#dc3545',
        self::WARNING => '#ffc107',
        self::INFO => '#17a2b8',
        self::CUSTOM => '#17a2b8'
    ];

    public readonly string $message;
    public readonly ?object $data;
    public readonly string $type;
    public readonly string $icon;
    public readonly string $iconColor;

    public readonly string $UUID = uniqid();

    public function __construct(string $message, ?object $data, string $type, string $icon, string $iconColor) {
        $this->message = $message;
        $this->data = $data;
        $this->type = $type;
        $this->icon = $icon;
        $this->iconColor = $iconColor;
        $this->UUID = uniqid('notification-', true);
    }

}