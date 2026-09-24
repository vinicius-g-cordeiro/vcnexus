<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);


namespace App\Shared\Schema\Compiler;

use App\Shared\Schema\Attributes\References;
use ReflectionProperty;
use ReflectionClass;
use App\Shared\Schema\Attributes\{Column, Comment, Nullable, PrimaryKey, Identity, Unique, Index, RowLevelSecurity, Policy, ForeignKey, Auditable, Timestamps, TenantScoped};


final class AttributeReader
{
    
    public static function indexes(?string $object) : array {
        $reflection = new ReflectionClass($object);
        $values = [];

        foreach($reflection->getAttributes() as $attributes) {
            if($attributes->getName() !== Index::class) continue;
            $values[] = $attributes->newInstance();
        }

        return $values;
    }

    /**
     * Get the timestamps columns from the class if attribute is present
     * @param ?string<class-string> $class 
     * @return ?array<Column>
     */
    public static function getTimestamps(?string $class): ?array
    {
        $reflection = new ReflectionClass($class);
        $attributes = $reflection->getAttributes(Timestamps::class);
        
        if(isset($attributes, $attributes[0]) === false) return null;
        
        $nullProperties = $attributes[0]->newInstance();
        $removedProperties = [];
        foreach($nullProperties as $property => $value) {
            $reflection  = new ReflectionProperty(Timestamps::class, $property);
            $attributes = $reflection->getAttributes(Column::class);

            if(isset($attributes, $attributes[0]) === false) continue;
            $column = $attributes[0]->newInstance();

            // Get nullable
            $attributes = $reflection->getAttributes(Nullable::class);
            if(isset($attributes, $attributes[0]) === true) {
                $column->nullable = $attributes[0]->newInstance()->nullable ?? false;
            }

            // Get primary key
            $attributes = $reflection->getAttributes(PrimaryKey::class);
            if(isset($attributes, $attributes[0]) === true) {
                $column->primary_key = $attributes[0]->newInstance()->primaryKey ?? false;
            }

            // Get identity
            $attributes = $reflection->getAttributes(Identity::class);
            if(isset($attributes, $attributes[0]) === true) {
                $column->identity = $attributes[0]->newInstance()->identity ?? false;
                $column->identity_generated = $attributes[0]->newInstance()->primaryKeyGenerated ?? 'GENERATED ALWAYS AS IDENTITY';
            }
            
            // Get comment
            $attributes = $reflection->getAttributes(Comment::class);
            if(isset($attributes, $attributes[0]) === true) {
                $column->comment = $attributes[0]->newInstance()->comment ?? false;
            }
            
            $column->name = $property;

            $values[$property] = object(...(array)$column);

        }

        return $values;
    }

    /**
     * Get the columns from the class
     * @param ?string<class-string> $class
     * @return ?array<Column>
     */
    public static function getColumns(?string $class): ?array
    {
        $reflection = new ReflectionClass($class);
        $values = [];
        $classes = [];

        while($reflection) {
            $classes[] = $reflection;
            $reflection = $reflection->getParentClass();
        }

        $classes = array_reverse($classes);

        foreach($classes as $class) {
            foreach($class->getProperties() as $property) {
                $propertyName = $property->getName();

                $attributes = $property->getAttributes(Column::class);

                if(isset($attributes, $attributes[0]) === false) continue;
                $column = $attributes[0]->newInstance();


                // Get nullable
                $attributes = $property->getAttributes(Nullable::class);
                if(isset($attributes, $attributes[0]) === true) {
                    $column->nullable = $attributes[0]->newInstance()->nullable ?? false;
                }

                // Get primary key
                $attributes = $property->getAttributes(PrimaryKey::class);
                if(isset($attributes, $attributes[0]) === true) {
                    $primaryKey = $attributes[0]->newInstance();
                    if(isset($primaryKey->primaryKey) === true) {
                        if(isset($primaryKey->key) === true) {
                            $column->primary_key = $primaryKey->key;
                        }
                    } 
                }

                // Get identity
                $attributes = $property->getAttributes(Identity::class);
                if(isset($attributes, $attributes[0]) === true) {
                    $column->identity = $attributes[0]->newInstance()->identity ?? false;
                    $column->identity_generated = $attributes[0]->newInstance()->primaryKeyGenerated ?? 'GENERATED ALWAYS AS IDENTITY';
                }

                $attributes = $property->getAttributes(References::class);
                if(isset($attributes, $attributes[0]) === true) {
                    $col = $attributes[0]->newInstance();
                    $column->references = $col->references ?? false;
                    $column->referencesClass = $col->references ?? false;
                    $column->referencesDeleteAction = $col->deleteAction ?? false;
                    $column->referencesColumns = $col->columns ?? false;
                }
                
                // Get comment
                $attributes = $property->getAttributes(Comment::class);
                if(isset($attributes, $attributes[0]) === true) {
                    $column->comment = $attributes[0]->newInstance()->comment ?? false;
                }
                
                $column->name = $propertyName;

                $values[$propertyName] = object(...(array)$column);
            }
        }

        return $values;
    }

    /**
     * Get the columns from the class
     * @param ?string<class-string> $class
     * @return ?array<Column>
     */
    public static function getAuditable(?string $class): ?array
    {
        $reflection = new ReflectionClass($class);
        $attributes = $reflection->getAttributes(Auditable::class);
        
        if(isset($attributes, $attributes[0]) === false) return null;
        
        $nullProperties = $attributes[0]->newInstance();
        $removedProperties = [];
        foreach($nullProperties as $property => $value) {
            $reflection  = new ReflectionProperty(Auditable::class, $property);
            $attributes = $reflection->getAttributes(Column::class);

            if(isset($attributes, $attributes[0]) === false) continue;
            $column = $attributes[0]->newInstance();

            // Get nullable
            $attributes = $reflection->getAttributes(Nullable::class);
            if(isset($attributes, $attributes[0]) === true) {
                $column->nullable = $attributes[0]->newInstance()->nullable ?? false;
            }

            // Get primary key
            $attributes = $reflection->getAttributes(PrimaryKey::class);
            if(isset($attributes, $attributes[0]) === true) {
                $column->primary_key = $attributes[0]->newInstance()->primaryKey ?? false;
            }

            // Get identity
            $attributes = $reflection->getAttributes(Identity::class);
            if(isset($attributes, $attributes[0]) === true) {
                $column->identity = $attributes[0]->newInstance()->identity ?? false;
                $column->identity_generated = $attributes[0]->newInstance()->primaryKeyGenerated ?? 'GENERATED ALWAYS AS IDENTITY';
            }
            
            // Get comment
            $attributes = $reflection->getAttributes(Comment::class);
            if(isset($attributes, $attributes[0]) === true) {
                $column->comment = $attributes[0]->newInstance()->comment ?? false;
            }
            
            $column->name = $property;

            $values[$property] = object(...(array)$column);

            
        }

        return $values;
    }

    public static function getTenantScoped(?string $class): ?array
    {
        /**
         * @var TenantScoped[]
         */
        $values = [];
        $reflectionClass = new ReflectionClass($class);
        $attributes = $reflectionClass->getAttributes(TenantScoped::class);
        $attributeClass = $attributes;

        if(isset($attributes, $attributes[0]) === false) return null;
        $nullProperties = $attributes[0]->newInstance();
        $removedProperties = [];
        foreach($nullProperties as $property => $value) {
            $reflection  = new ReflectionProperty(TenantScoped::class, $property);
            $attributes = $reflection->getAttributes(Column::class);

            if(isset($attributes, $attributes[0]) === false) continue;
            $column = $attributes[0]->newInstance();

            // Find the nullable property 
            $att = $attributeClass[0]->newInstance();
            $reflection = new ReflectionProperty(TenantScoped::class, 'nullable');
            $nullable = $reflection->getValue($att);
            if(isset($nullable) === true) {
                $column->nullable = $nullable;
                if($column->nullable === false) {
                    $column->default = 1;
                }
            }

            
            // Get primary key
            $attributes = $reflection->getAttributes(PrimaryKey::class);
            if(isset($attributes, $attributes[0]) === true) {
                $column->primary_key = $attributes[0]->newInstance()->primaryKey ?? false;
            }

            // Get identity
            $attributes = $reflection->getAttributes(Identity::class);
            if(isset($attributes, $attributes[0]) === true) {
                $column->identity = $attributes[0]->newInstance()->identity ?? false;
                $column->identity_generated = $attributes[0]->newInstance()->primaryKeyGenerated ?? 'GENERATED ALWAYS AS IDENTITY';
            }
            
            // Get comment
            $attributes = $reflection->getAttributes(Comment::class);
            if(isset($attributes, $attributes[0]) === true) {
                $column->comment = $attributes[0]->newInstance()->comment ?? false;
            }
            
            $column->name = $property;

            $values[$property] = object(...(array)$column);

            
        }

        return $values;
    }

    /** @return Unique[] */
    public static function getUniqueConstraints(string $schemaClass): array
    {
        $reflection = new ReflectionClass($schemaClass);

        return array_map(
            fn($attribute) => $attribute->newInstance(),
            $reflection->getAttributes(Unique::class)
        );
    }

    /** @return ForeignKey[] */
    public static function getForeignKeys(string $schemaClass): array
    {
        $reflection = new ReflectionClass($schemaClass);

        return array_map(
            fn($attribute) => $attribute->newInstance(),
            $reflection->getAttributes(ForeignKey::class)
        );
    }

    public static function getPrimaryKeys(string $schemaClass): array
    {
        $reflection = new ReflectionClass($schemaClass);

        return array_map(
            fn($attribute) => $attribute->newInstance(),
            $reflection->getAttributes(PrimaryKey::class)
        );
    }


    public static function getPolicies(string $schemaClass): array
    {
        $reflection = new ReflectionClass($schemaClass);

        return array_map(
            fn($attribute) => $attribute->newInstance(),
            $reflection->getAttributes(Policy::class)
        );
    }

    public static function getRowLevelSecurity(string $schemaClass): array
    {
        $reflection = new ReflectionClass($schemaClass);

        return array_map(
            fn($attribute) => $attribute->newInstance(),
            $reflection->getAttributes(RowLevelSecurity::class)
        );
    }

    public static function getComments(string $schemaClass): array
    {
        $comments = [];
        $reflection = new ReflectionClass($schemaClass);
        $tableName = $reflection->getProperty('table');
        foreach($reflection->getProperties() as $property) {
            $propertyName = $property->getName();
            $attributes = $property->getAttributes(Comment::class);
            if(isset($attributes, $attributes[0]) === false) continue;

            $column = $attributes[0]->newInstance();
            $column->name = $propertyName;
            $column->table = $tableName;
            $comments[] = object(...(array)$column);
        }

        return $comments;
    }



}