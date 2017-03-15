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
                [
                    'type' => 'groups',
                    'id' => $groupId,
                    'attributes' => [
                        'name' => $groupName,
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
