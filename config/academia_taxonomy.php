<?php

/**
 * The public category taxonomy.
 *
 * ---------------------------------------------------------------------------
 * WHY THIS MAPPING EXISTS
 *
 * The source catalogue was authored under 6 broad parent categories and 46
 * subcategories. The specification calls for 12 public categories, which is
 * the better structure for search: "SAP training", "cybersecurity training"
 * and "leadership training" are high-intent queries that a generic
 * "Technology & Digital Skills" hub does not target.
 *
 * Rather than re-authoring 524 rows, the 12 categories are composed here from
 * the existing subcategories. Every one of the 524 courses lands in exactly
 * one category, and the importer reports any subcategory that is not mapped —
 * because an unmapped subcategory would silently disappear from every
 * category page while still existing in the catalogue.
 *
 * Verified coverage (524 total, 0 orphans, 0 empty categories).
 *
 * NOTE FOR THE CONTENT TEAM: Marketing & Sales (21) and Business & Management
 * (22) are the two thin hubs. Either commission ~15 more courses in each, or
 * merge Business & Management into Leadership & Soft Skills and run 11.
 * ---------------------------------------------------------------------------
 */
return [
    'map' => [

        'Business & Management' => [
            'icon'    => 'brief',
            'color'   => 'orange',
            'summary' => 'Strategy, planning and the administrative craft that keeps an organisation coherent.',
            'subcategories' => [
                'Strategy & Business Development',
                'Business Support & Administration',
            ],
        ],

        'Leadership & Soft Skills' => [
            'icon'    => 'users',
            'color'   => 'green',
            'summary' => 'Leading people, influencing without authority, and working well under pressure.',
            'subcategories' => [
                'Leadership Development',
                'Communication & Influence',
                'Personal Effectiveness',
            ],
        ],

        'Finance & Accounting' => [
            'icon'    => 'chart',
            'color'   => 'gold',
            'summary' => 'Reporting, control, modelling and the regulation around them.',
            'subcategories' => [
                'Financial Accounting & Reporting',
                'Management Accounting & Control',
                'Corporate Finance & Modelling',
                'Tax, Audit & Assurance',
                'Banking, Insurance & Capital Markets',
                'Public & Non-Profit Finance',
            ],
        ],

        'SAP & ERP' => [
            'icon'    => 'building',
            'color'   => 'orange',
            'summary' => 'SAP and enterprise systems taught in a sandbox that mirrors your own environment.',
            'subcategories' => [
                'SAP & Enterprise Systems',
                'Finance Systems',
            ],
        ],

        'Data Analytics & BI' => [
            'icon'    => 'chart',
            'color'   => 'green',
            'summary' => 'From spreadsheets to self-service analytics, with your own data on day one.',
            'subcategories' => [
                'Data Analytics & BI',
                'Excel & Spreadsheets',
            ],
        ],

        'AI & Digital Transformation' => [
            'icon'    => 'spark',
            'color'   => 'gold',
            'summary' => 'Where generative AI genuinely saves time, and where it is still theatre.',
            'subcategories' => [
                'Artificial Intelligence',
                'Design & Digital Product',
            ],
        ],

        'Project Management & Agile' => [
            'icon'    => 'check',
            'color'   => 'orange',
            'summary' => 'Delivery method, governance and the certifications that go with them.',
            'subcategories' => [
                'Project Management',
                'Agile & Product',
            ],
        ],

        'Supply Chain & Operations' => [
            'icon'    => 'truck',
            'color'   => 'green',
            'summary' => 'Planning, procurement, logistics and operational excellence.',
            'subcategories' => [
                'Supply Chain Management',
                'Procurement & Sourcing',
                'Logistics & Warehousing',
                'Operational Excellence',
                'Quality & Manufacturing Systems',
                'Maintenance & Asset Management',
                'Retail & E-commerce Operations',
            ],
        ],

        'Human Resources' => [
            'icon'    => 'people',
            'color'   => 'gold',
            'summary' => 'Hiring, developing, rewarding and retaining people, and the law around it.',
            'subcategories' => [
                'HR Management & Strategy',
                'Talent Acquisition',
                'Performance & Development',
                'Employee Experience & Culture',
                'Reward & HR Analytics',
                'Employee Relations & Labour',
                'Global Mobility & International HR',
            ],
        ],

        'Compliance & Risk Management' => [
            'icon'    => 'shield',
            'color'   => 'orange',
            'summary' => 'Privacy, risk, governance and the European regulation that drives them.',
            'subcategories' => [
                'Data Protection & Privacy',
                'Risk Management',
                'Governance & Ethics',
                'Financial Crime & Regulatory',
                'Health, Safety & Environment',
                'Legal & Contracting',
                'Information Governance',
            ],
        ],

        'Marketing & Sales' => [
            'icon'    => 'spark',
            'color'   => 'green',
            'summary' => 'Winning and keeping customers, in the channels that actually convert.',
            'subcategories' => [
                'Customer & Sales Excellence',
                'Digital Marketing',
            ],
        ],

        'IT & Cybersecurity' => [
            'icon'    => 'code',
            'color'   => 'gold',
            'summary' => 'Building, running and defending the systems the business depends on.',
            'subcategories' => [
                'Cybersecurity',
                'Cloud & Infrastructure',
                'IT Service & Architecture',
                'Programming & Development',
            ],
        ],
    ],
];
