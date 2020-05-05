<?php

use Naoray\BlueprintNovaAddon\Tasks\AddIdentifierField;
use Naoray\BlueprintNovaAddon\Tasks\AddRegularFields;
use Naoray\BlueprintNovaAddon\Tasks\AddRelationshipFields;
use Naoray\BlueprintNovaAddon\Tasks\AddTimestampFields;
use Naoray\BlueprintNovaAddon\Tasks\RemapImports;

return [
    'field_tasks' => [
        AddIdentifierField::class,
        AddRegularFields::class,
        AddRelationshipFields::class,
        AddTimestampFields::class,
        RemapImports::class,
    ],
];