<?php
namespace david50407\ConvertAthenaQueryResultstoArray\Tests;

use PHPUnit\Framework\TestCase;
use david50407\ConvertAthenaQueryResultstoArray\ConvertAthenaQueryResultstoArray;

class ConvertAthenaQueryResultstoArrayTest extends TestCase
{
    public function testCast()
    {
        $testCase = [
            [
                'metadata' => ['Type' => 'integer'],
                'value' => '30',
                'expected' => 30
            ],
            [
                'metadata' => ['Type' => 'double'],
                'value' => '30.333',
                'expected' => 30.333
            ],
            [
                'metadata' => ['Type' => 'varchar'],
                'value' => '30',
                'expected' => '30'
            ]
        ];

        $reflection = new \ReflectionClass(new ConvertAthenaQueryResultstoArray);
        $method = $reflection->getMethod('cast');
        $method->setAccessible(true);
        foreach ($testCase as $test) {
            $actual = $method->invoke(null, $test['metadata'], $test['value']);
            $this->assertSame($test['expected'], $actual, json_encode($test));
        }
    }

    public function testConvert()
    {
        $resultSet = [
            'Rows' => [
                [
                    'Data' => [
                        ['VarCharValue' => 'column_name1'],
                        ['VarCharValue' => 'column_name2']
                    ]
                ],
                [
                    'Data' => [
                        ['VarCharValue' => 'value1'],
                        ['VarCharValue' => '1']
                    ]
                ],
                [
                    'Data' => [
                        ['VarCharValue' => 'value2'],
                        ['VarCharValue' => '2']
                    ]
                ],
                [
                    'Data' => [
                        ['VarCharValue' => 'value3'],
                        ['VarCharValue' => '3']
                    ]
                ]
            ],
            'ResultSetMetadata' => [
                'ColumnInfo' => [
                    [
                        'CatalogName' => 'hive',
                        'SchemaName' => '',
                        'TableName' => '',
                        'Name' => 'column_name1',
                        'Label' => 'column_name1',
                        'Type' => 'varchar',
                        'Precision' => 2147483647,
                        'Scale' => 0,
                        'Nullable' => 'UNKNOWN',
                        'CaseSensitive' => true
                    ],
                    [
                        'CatalogName' => 'hive',
                        'SchemaName' => '',
                        'TableName' => '',
                        'Name' => 'column_name2',
                        'Label' => 'column_name2',
                        'Type' => 'integer',
                        'Precision' => 10,
                        'Scale' => 0,
                        'Nullable' => 'UNKNOWN',
                        'CaseSensitive' => false
                    ]
                ]
            ]
        ];
        $expected = [
            [
                'column_name1' => 'value1',
                'column_name2' => 1
            ],
            [
                'column_name1' => 'value2',
                'column_name2' => 2
            ],
            [
                'column_name1' => 'value3',
                'column_name2' => 3
            ]
        ];
        $this->assertSame(
            $expected,
            ConvertAthenaQueryResultstoArray::convert($resultSet)
        );
    }
}
