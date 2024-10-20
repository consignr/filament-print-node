<?php

// config for Consignr/filament-print-node
return [
    'cluster' => [        
        'navigation_group' => null,
        'navigation_label' => 'Print Node',
        'navigation_icon' => 'heroicon-o-command-line',
        'navigation_sort' => null
    ],        
    'computers' => [  
        'label' => 'Computer',
        'plural_label' => 'Computers',   
        'navigation_label' => 'Computers',
        'navigation_icon' => 'heroicon-o-computer-desktop',
        'navigation_sort' => 1,
        'navigation_count_badge' => false,
    ],
    'printers' => [  
        'label' => 'Printer',
        'plural_label' => 'Printers',   
        'navigation_label' => 'Printers',
        'navigation_icon' => 'heroicon-o-printer',
        'navigation_sort' => 2,            
        'navigation_count_badge' => false,
    ],
    'print_jobs' => [  
        'label' => 'Print Job',
        'plural_label' => 'Print Jobs',
        'navigation_label' => 'Print Jobs',
        'navigation_icon' => 'heroicon-o-queue-list',
        'navigation_sort' => 3,              
        'navigation_count_badge' => false,
    ]
];