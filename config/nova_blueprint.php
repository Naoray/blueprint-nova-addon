<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Resource Timestamps
    |--------------------------------------------------------------------------
    |
    | Nova Blueprint by default adds the timestamp fields 'created_at',
    | 'updated_at' and 'deleted_at' (if model uses SoftDeletes Trait) to
    | the generated resources. if you want to prevent the generator from
    | adding these fields set this option to `false`.
    |
    */

    'timestamps' => true,

    /*
    |--------------------------------------------------------------------------
    | Nova Namespace
    |--------------------------------------------------------------------------
    |
    | Nova Blueprint recommends sticking to the standard `App\Nova` namespace
    | used in the Nova docs all your Nova resources.  However, if you want to
    | create these resources in a different namespace, you can change the
    | default namespace below.
    |
    | This namespace will be prefixed with the namespace defined in Laravel
    | Blueprint.
    |
    */
    'namespace' => 'Nova',

    /*
    |--------------------------------------------------------------------------
    | Nova Component Namespaces
    |--------------------------------------------------------------------------
    |
    | Nova Blueprint uses the namespaces as defined in the Laravel docs for
    | your Nova Components. These too, can be customized to your liking.
    |
    | The Nova components will be prefixed with the Nova namespace and the
    | Blueprint namespace, so a default action namespace would be
    | `App\Nova\Actions`.
    |
    | Use null if you want to put the component in the root of your Nova
    | namespace.
    |
    */
    'resource_namespace' => null,
];
