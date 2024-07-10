<?php
return [
    'free' => [
        'monthly_price' => 0,
        'yearly_price' => 0,
        'database_control' => false,
        'max_tables' => 0,
        'max_projects' => 1,
        'max_pages_per_project' => 3,
        'design_control' => 'limited',
        'template_access' => 'default',
    ],
    'individual' => [
        'monthly_price' => 9.99,
        'yearly_price' => 99.99,
        'database_control' => true,
        'max_tables' => 6,
        'max_projects' => 3,
        'max_pages_per_project' => 'unlimited',
        'design_control' => 'unlimited',
        'template_access' => 'all',
    ],
    'enterprise' => [
        'monthly_price' => 24.99,
        'yearly_price' => 239.99,
        'database_control' => true,
        'max_tables' => 'unlimited',
        'max_projects' => 'unlimited',
        'max_pages_per_project' => 'unlimited',
        'design_control' => 'unlimited',
        'template_access' => 'all',
    ],
];
