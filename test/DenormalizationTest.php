<?php

class DenormalizationTest extends \PHPUnit\Framework\TestCase
{
    public function testDenormalizeSimple()
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
        ], jsonapi_denormalize([
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
        ]));
    }

    public function testDenormalizeDuplicate()
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
        ], jsonapi_denormalize([
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
        ]));
    }

    public function testDenormalizeDeep()
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
        ], jsonapi_denormalize([
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
        ]));
    }
}
