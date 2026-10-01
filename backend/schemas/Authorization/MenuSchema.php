<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);


namespace App\Schemas\Authorization;

use App\Schemas\AbstractSchema;
use App\Shared\Schema\Attributes\{Column, Nullable, Comment, ForeignKey, TenantScoped};


#[ForeignKey(references: 'menus', foreignKeys: ['parent_id'], columns: ['id'], deleteAction: 'CASCADE')]
#[TenantScoped(tenant_id: 'tenant_id', nullable: true)]
final class MenuSchema extends AbstractSchema
{
    public string $table = 'menus';

    #[Column(type: 'bigint')]
    #[Nullable(nullable: true)]
    #[Comment(comment: 'Parent menu id. eg: if is null is a root menu item, if not then is a child menu item from the parent id menu item. ')]
    public ?int $parent_id;

    #[Column(type: 'varchar', length: 100)]
    #[Nullable(nullable: false)]
    #[Comment(comment: 'Menu label. eg: Users')]
    public ?string $label;

    #[Column(type: 'varchar', length: 100)]
    #[Nullable(nullable: true)]
    #[Comment(comment: 'Menu icon. eg: bi bi-person-fill for Bootstrap Icons')]
    public ?string $icon;

    #[Column(type: 'varchar', length: 100)]
    #[Nullable(nullable: true)]
    #[Comment(comment: 'Route name on Vue.js router. eg: users.index')]
    public ?string $route;


    #[Column(type: 'bigint')]
    #[Nullable(nullable: false)]
    #[Comment(comment: 'Menu order. eg: 1 for first position')]
    public ?int $order;

    #[Column(type: 'varchar::array', length: 100, default: null)]
    #[Nullable(nullable: true)]
    #[Comment(comment: 'Menu permissions. eg: ["users.index", "users.create"]')]
    public ?array $permissions;
}
