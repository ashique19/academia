<?php

/**
 * LIVE WordPress paths → Laravel paths.
 *
 * Subcategory URLs are not listed one by one. Each live child slug matches
 * Str::slug() of a name in config/academia_taxonomy.php, and that file already
 * says which of the 12 public categories the child belongs to. The parent
 * hubs below are the six live pillars, which do not line up 1:1.
 *
 * technology-digital-skills was split across AI, Data, IT & Cybersecurity,
 * SAP & ERP, and (for digital marketing) Marketing & Sales. Its child URLs
 * redirect to those categories. The parent hub itself goes to /courses.
 *
 * business-management on the live site also contained leadership, project
 * management and sales. Those children redirect to their own categories. The
 * parent hub redirects to the narrower Business & Management category.
 */
return [

    'parents' => [
        'business-management' => 'business-management',
        'compliance-risk-governance' => 'compliance-risk-management',
        'finance-accounting' => 'finance-accounting',
        'human-resources' => 'human-resources',
        'supply-chain-operations' => 'supply-chain-operations',
        'technology-digital-skills' => null,
    ],

    'delivery' => [
        'online' => '/online-training',
        'classroom' => '/classroom-training',
        'onsite' => '/corporate-training',
        'self-paced' => '/courses?mode[0]=self-paced',
    ],

];
