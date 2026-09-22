<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Tenant\DTOs;

use App\Shared\Domain\DataTransferObjectInterface;
use Symfony\Component\Validator\Constraints as Assert;


// {
//   "fantasy_name": "Puro Sabor",
//   "trade_name": "Açai - Puro Sabor",
//   "type": "1",
//   "tax_id": "62.728.369/0001-72",
//   "municipal_registration": "",
//   "state_registration": "",
//   "website": "https://purosabor.vcnexus.com",
//   "domain": "purosabor.vcnexus.com",
//   "slug": "purosabor",
//   "description": "Venda de açai, din-dins e outras gulozeimas",
//   "logo": "https://purosabor.vcnexus.com/logo.png",
//   "app_name": "Açai - Puro Sabor",
//   "primary_color": "#FF0000",
//   "accent_color": "#00FF00",
//   "text_color": "#000000",
//   "background_color": "#FFFFFF",
//   "font_style": "sans-serif",
//   "button_style": "flat",
//   "active": 1,
//   "subscription_type": 1,
//   "subscription_plan": 1,
//   "subscription_status": 1,
//   "created_by": 1
// }
final class TenantStoreInRequest implements DataTransferObjectInterface
{

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 255)]
        public ?string $fantasy_name,
        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 255)]
        public ?string $trade_name,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 3, max: 255)]
        #[Assert\Regex(pattern: '/^\d{2}\.\d{3}\.\d{3}\/\d{4}\-\d{2}$/')]
        public ?string $tax_id,
        #[Assert\Type('string')]
        #[Assert\Length(min: 3, max: 255)]
        #[Assert\Url()]
        public ?string $website,
        #[Assert\Type('string')]
        #[Assert\Length(min: 3, max: 255)]
        public ?string $domain,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 3, max: 255)]
        #[Assert\Regex(pattern: '/^[a-z0-9]+(?:-[a-z0-9]+)*$/')]
        public string $slug,
        public ?string $state_registration,
        public ?string $municipal_registration,
        #[Assert\Type('int')]
        public ?int $active,
        #[Assert\Type('int')]
        public ?int $subscription_type,
        #[Assert\Type('int')]
        public ?int $subscription_status,
        #[Assert\Type('int')]
        public ?int $created_by,
        #[Assert\Type('int')]
        public ?int $type,
        public ?string $description,
        public ?string $logo,
        public ?string $app_name,
        public ?string $primary_color,
        public ?string $accent_color,
        public ?string $text_color,
        public ?string $background_color,
        public ?string $font_style,
        public ?string $button_style
    ) {
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public static function fromArray(array $data): self            
    {
        return new self(
            fantasy_name: $data['fantasy_name'] ?? null,
            trade_name: $data['trade_name'] ?? null,
            tax_id: $data['tax_id'] ?? null,
            website: $data['website'] ?? null,
            domain: $data['domain'] ?? null,
            slug: $data['slug'] ?? null,
            state_registration: $data['state_registration'] ?? null,
            municipal_registration: $data['municipal_registration'] ?? null,
            active: isset($data['active']) ? (int)$data['active'] : null,
            subscription_type: isset($data['subscription_type']) ? (int)$data['subscription_type'] : null,
            subscription_status: isset($data['subscription_status']) ? (int)$data['subscription_status'] : null,
            created_by: isset($data['created_by']) ? (int)$data['created_by'] : null,
            type: isset($data['type']) ? (int)$data['type'] : null,
            description: $data['description'] ?? null,
            logo: $data['logo'] ?? null,
            app_name: $data['app_name'] ?? null,
            primary_color: $data['primary_color'] ?? null,
            accent_color: $data['accent_color'] ?? null,
            text_color: $data['text_color'] ?? null,
            background_color: $data['background_color'] ?? null,
            font_style: $data['font_style'] ?? null,
            button_style: $data['button_style'] ?? null,
        );
    }
}
