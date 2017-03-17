<?php

class NormalizationTest extends \PHPUnit\Framework\TestCase
{
    public function testNormalizeSimple()
    {
        $employeeId = uniqid();
        $employeeName = uniqid();
        $companyId = uniqid();
        $companyName = uniqid();

        $this->assertEquals([
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
        ], jsonapi_normalize([
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
        ]));
    }

    public function testNormalizeNoIncludes()
    {
        $employeeId = uniqid();
        $employeeName = uniqid();

        $this->assertEquals([
            'data' => [
                'type' => 'employees',
                'id' => $employeeId,
                'attributes' => [
                    'name' => $employeeName,
                ],
            ],
        ], jsonapi_normalize([
            'data' => [
                'type' => 'employees',
                'id' => $employeeId,
                'attributes' => [
                    'name' => $employeeName,
                ],
            ],
        ]));
    }

    public function testNormalizeDuplicate()
    {
        $employeeId = uniqid();
        $employeeName = uniqid();
        $companyId = uniqid();
        $companyName = uniqid();

        $this->assertEquals([
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
        ], jsonapi_normalize([
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
        ]));
    }

    public function testNormalizeDatas()
    {
        $employee1Id = uniqid();
        $employee1Name = uniqid();
        $employee2Id = uniqid();
        $employee2Name = uniqid();
        $companyId = uniqid();
        $companyName = uniqid();

        $this->assertEquals([
            'data' => [
                [
                    'type' => 'employees',
                    'id' => $employee1Id,
                    'attributes' => [
                        'name' => $employee1Name,
                    ],
                    'relationships' => [
                        'company' => [
                            'data' => ['type' => 'companies', 'id' => $companyId],
                        ],
                    ],
                ],
                [
                    'type' => 'employees',
                    'id' => $employee2Id,
                    'attributes' => [
                        'name' => $employee2Name,
                    ],
                    'relationships' => [
                        'company' => [
                            'data' => ['type' => 'companies', 'id' => $companyId],
                        ],
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
        ], jsonapi_normalize([
            'data' => [
                [
                    'type' => 'employees',
                    'id' => $employee1Id,
                    'attributes' => [
                        'name' => $employee1Name,
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
                [
                    'type' => 'employees',
                    'id' => $employee2Id,
                    'attributes' => [
                        'name' => $employee2Name,
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
        ]));
    }

    public function testNormalizeDatasData()
    {
        $employee1Id = uniqid();
        $employee1Name = uniqid();
        $employee2Id = uniqid();
        $employee2Name = uniqid();
        $companyId = uniqid();
        $companyName = uniqid();

        $this->assertEquals([
            'data' => [
                [
                    'type' => 'employees',
                    'id' => $employee1Id,
                    'attributes' => [
                        'name' => $employee1Name,
                    ],
                    'relationships' => [
                        'company' => [
                            'data' => ['type' => 'companies', 'id' => $companyId],
                        ],
                    ],
                ],
                [
                    'type' => 'employees',
                    'id' => $employee2Id,
                    'attributes' => [
                        'name' => $employee2Name,
                    ],
                    'relationships' => [
                        'company' => [
                            'data' => ['type' => 'companies', 'id' => $companyId],
                        ],
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
        ], jsonapi_normalize([
            'data' => [
                [
                    'data' => [
                        'type' => 'employees',
                        'id' => $employee1Id,
                        'attributes' => [
                            'name' => $employee1Name,
                        ],
                        'relationships' => [
                            'company' => [
                                'data' => [
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
                    ]
                ],
                [
                    'data' => [
                        'type' => 'employees',
                        'id' => $employee2Id,
                        'attributes' => [
                            'name' => $employee2Name,
                        ],
                        'relationships' => [
                            'company' => [
                                'data' => [
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
                ],
            ],
        ]));
    }

    public function testNormalizeDeep()
    {
        $employeeId = uniqid();
        $employeeName = uniqid();
        $companyId = uniqid();
        $companyName = uniqid();
        $groupId = uniqid();
        $groupName = uniqid();

        $this->assertEquals([
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
        ], jsonapi_normalize([
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
        ]));
    }

    public function testNormalizeDeepRecursed()
    {
        $employeeId = uniqid();
        $employeeName = uniqid();
        $companyId = uniqid();
        $companyName = uniqid();
        $groupId = uniqid();
        $groupName = uniqid();

        $this->assertEquals([
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
        ], jsonapi_normalize([
            'data' => [
                'type' => 'employees',
                'id' => $employeeId,
                'attributes' => [
                    'name' => $employeeName,
                ],
                'relationships' => [
                    'company' => jsonapi_normalize([
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
                    ]),
                ],
            ],
        ]));
    }

    public function testNormalizeLayeredInclude()
    {
        $employeeId = uniqid();
        $employeeName = uniqid();
        $companyId = uniqid();
        $companyName = uniqid();
        $groupId = uniqid();
        $groupName = uniqid();

        $this->assertEquals([
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
        ], jsonapi_normalize([
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
                        'included' => [
                            [
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
        ]));
    }
}
