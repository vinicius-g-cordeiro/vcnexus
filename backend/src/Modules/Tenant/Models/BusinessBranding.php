<?php
/**
 * GENERATED from App\Schemas\Tenant\BusinessBrandingSchema — do not edit directly. Edit the schema and recompile.
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0.0
 * @date 24/09/2026
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Tenant\Models;

use App\Shared\Schema\BaseModel;

final class BusinessBranding extends BaseModel
{
    public ?int $id;
    public ?string $uuid;
    public ?int $active;
    public int $business_id;
    public ?string $logo;
    public ?string $app_name;
    public ?string $primary_color;
    public string $accent_color;
    public string $text_color;
    public string $background_color;
    public string $font_style;
    public string $button_style;
    public function __construct() {}

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public static function fromArray(array $data): self
    {
        $newObject = new static();
        $newObject->id = isset($data['id']) ? (int) $data['id'] : null;
        $newObject->uuid = isset($data['uuid']) ? $data['uuid'] : null;
        $newObject->active = isset($data['active']) ? (int) $data['active'] : null;
        $newObject->business_id = isset($data['business_id']) ? (int) $data['business_id'] : null;
        $newObject->logo = isset($data['logo']) ? $data['logo'] : null;
        $newObject->app_name = isset($data['app_name']) ? $data['app_name'] : null;
        $newObject->primary_color = isset($data['primary_color']) ? $data['primary_color'] : null;
        $newObject->accent_color = isset($data['accent_color']) ? $data['accent_color'] : null;
        $newObject->text_color = isset($data['text_color']) ? $data['text_color'] : null;
        $newObject->background_color = isset($data['background_color']) ? $data['background_color'] : null;
        $newObject->font_style = isset($data['font_style']) ? $data['font_style'] : null;
        $newObject->button_style = isset($data['button_style']) ? $data['button_style'] : null;
        return $newObject;
    }
}