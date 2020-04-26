<?php

/**
 * Validation config
 * https://github.com/editor-js/editorjs-php
 */
return [
    'tools' => [
        'header' => [
            'text' => [
                'type' => 'string',
            ],
            'level' => [
                'type' => 'int',
                'canBeOnly' => [4, 5, 6]
            ]
        ],
        'paragraph' => [
            'text' => [
                'type' => 'string',
                'allowedTags' => 'i,b,u,a[href],span[class],code[class],mark[class]'
            ]
        ],
        'list' => [
            'style' => [
                'type' => 'string',
                'canBeOnly' =>
                    [
                        0 => 'ordered',
                        1 => 'unordered',
                    ],
            ],
            'items' => [
                'type' => 'array',
                'data' => [
                    '-' => [
                        'type' => 'string',
                        'allowedTags' => 'i,b,u',
                    ],
                ],
            ],
        ],
        'image' => [
            'file' => [
                'type' => 'array',
                'data' => [
                    'url' => [
                        'type' => 'string',
                    ],
                    'thumbnails' => [
                        'type' => 'array',
                        'required' => false,
                        'data' => [
                            '-' => [
                                'type' => 'string',
                            ]
                        ],
                    ]
                ],
            ],
            'caption' => [
                'type' => 'string'
            ],
            'withBorder' => [
                'type' => 'boolean'
            ],
            'withBackground' => [
                'type' => 'boolean'
            ],
            'stretched' => [
                'type' => 'boolean'
            ]
        ],
        'code' => [
            'code' => [
                'type' => 'string'
            ]
        ],
        'linkTool' => [
            'link' => [
                'type' => 'string'
            ],
            'meta' => [
                'type' => 'array',
                'data' => [
                    'title' => [
                        'type' => 'string',
                    ],
                    'description' => [
                        'type' => 'string',
                        'required' => false,
                    ],
                    'image' => [
                        'type' => 'array',
                        'required' => false,
                        'data' => [
                            'url' => [
                                'type' => 'string',
                                'required' => false,
                            ],
                        ]
                    ]
                ]
            ]
        ],
        'checklist' => [
            'items' => [
                'type' => 'array',
                'data' => [
                    '-' => [
                        'type' => 'array',
                        'data' => [
                            'text' => [
                                'type' => 'string',
                                'required' => false
                            ],
                            'checked' => [
                                'type' => 'boolean',
                                'required' => false
                            ],
                        ],

                    ],
                ],
            ],
        ],
        'delimiter' => [],
        'table' => [
            'content' => [
                'type' => 'array',
                'data' => [
                    '-' => [
                        'type' => 'array',
                        'data' => [
                            '-' => [
                                'type' => 'string',
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'raw' => [
            'html' => [
                'type' => 'string',
                'allowedTags' => '*',
            ]
        ],
        'embed' => [
            'service' => [
                'type' => 'string'
            ],
            'source' => [
                'type' => 'string'
            ],
            'embed' => [
                'type' => 'string'
            ],
            'width' => [
                'type' => 'int'
            ],
            'height' => [
                'type' => 'int'
            ],
            'caption' => [
                'type' => 'string',
                'required' => false,
            ],
        ],
        'quote' => [
            'text' => 'string',
            'caption' => 'string',
            'alignment' => [
                'type' => 'string',
                'canBeOnly' => ['left', 'center', 'right']
            ]
        ],
    ]
];
