<?php

class NormalizationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataSimple
     */
    public function testNormalize($denormal, $normal)
    {
        $this->assertEquals($normal, jsonapi_normalize($denormal));
    }

    /**
     * @dataProvider dataSimple
     */
    public function testDenormalize($denormal, $normal)
    {
        $this->assertEquals($denormal, jsonapi_denormalize($normal));
    }

    public function dataSimple()
    {
        $employeeId = uniqid();
        $employeeName = uniqid();
        $companyId = uniqid();
        $companyName = uniqid();
        $groupId = uniqid();
        $groupName = uniqid();

        return [
            // One level relationships

            [
                [
                    'data' => [
                        'type' => 'employees',
                        'id' => $employeeId,
                        'attributes' => [
                            'name' => $employeeName,
                        ],
                        'relationships' => [
                            'company' => [
                                'data' => [
                                    'type' => 'companies',
                                    'id' => $companyId,
                                    'attributes' => [
                                        'name' => $companyName,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'data' => [
                        'type' => 'employees',
                        'id' => $employeeId,
                        'attributes' => [
                            'name' => $employeeName,
                        ],
                        'relationships' => [
                            'company' => [
                                'data' => ['type' => 'companies', 'id' => $companyId],
                            ],
                        ],
                    ],
                    'included' => [
                        [
                            'type' => 'companies',
                            'id' => $companyId,
                            'attributes' => [
                                'name' => $companyName,
                            ],
                        ],
                    ],
                ],
            ],


            // One level, duplicate company

            [
                [
                    'data' => [
                        'type' => 'employees',
                        'id' => $employeeId,
                        'attributes' => [
                            'name' => $employeeName,
                        ],
                        'relationships' => [
                            'company' => [
                                'data' => [
                                    'type' => 'companies',
                                    'id' => $companyId,
                                    'attributes' => [
                                        'name' => $companyName,
                                    ],
                                ],
                            ],
                            'duplicateCompany' => [
                                'data' => [
                                    'type' => 'companies',
                                    'id' => $companyId,
                                    'attributes' => [
                                        'name' => $companyName,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'data' => [
                        'type' => 'employees',
                        'id' => $employeeId,
                        'attributes' => [
                            'name' => $employeeName,
                        ],
                        'relationships' => [
                            'company' => [
                                'data' => ['type' => 'companies', 'id' => $companyId],
                            ],
                            'duplicateCompany' => [
                                'data' => ['type' => 'companies', 'id' => $companyId],
                            ],
                        ],
                    ],
                    'included' => [
                        [
                            'type' => 'companies',
                            'id' => $companyId,
                            'attributes' => [
                                'name' => $companyName,
                            ],
                        ],
                    ],
                ],
            ],


            // Two levels relationships

            [
                [
                    'data' => [
                        'type' => 'employees',
                        'id' => $employeeId,
                        'attributes' => [
                            'name' => $employeeName,
                        ],
                        'relationships' => [
                            'company' => [
                                'data' => [
                                    'type' => 'companies',
                                    'id' => $companyId,
                                    'attributes' => [
                                        'name' => $companyName,
                                    ],
                                    'relationships' => [
                                        'group' => [
                                            'data' => [
                                                'type' => 'groups',
                                                'id' => $groupId,
                                                'attributes' => [
                                                    'name' => $groupName,
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'data' => [
                        'type' => 'employees',
                        'id' => $employeeId,
                        'attributes' => [
                            'name' => $employeeName,
                        ],
                        'relationships' => [
                            'company' => [
                                'data' => ['type' => 'companies', 'id' => $companyId],
                            ],
                        ],
                    ],
                    'included' => [
                        [
                            'type' => 'groups',
                            'id' => $groupId,
                            'attributes' => [
                                'name' => $groupName,
                            ],
                        ],
                        [
                            'type' => 'companies',
                            'id' => $companyId,
                            'attributes' => [
                                'name' => $companyName,
                            ],
                            'relationships' => [
                                'group' => [
                                    'data' => ['type' => 'groups', 'id' => $groupId],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
