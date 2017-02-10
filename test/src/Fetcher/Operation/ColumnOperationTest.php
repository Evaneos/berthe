<?php
namespace Berthe\Test\Fetcher\Operation;

use Berthe\Fetcher;
use Berthe\Fetcher\Operation\ColumnOperation;

/**
 * Class ColumnOperationTest
 **/
class ColumnOperationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_should_throw_an_exception_if_fetcher_type_is_not_valid() {
        new ColumnOperation(Fetcher::TYPE_LIKE, 'column');
    }

    /**
     * @test
     */
    public function it_should_generate_correct_sql_for_equal_fetcher_type() {
        $columnOperation = new ColumnOperation(Fetcher::TYPE_EQ, 'columnB');
        $operationValue = $columnOperation->getOperationValue('columnA');

        $this->assertNotEmpty($operationValue);

        $this->assertEquals('columnA = columnB', $operationValue[0]);
        $this->assertEmpty($operationValue[1]);
    }

    /**
     * @test
     */
    public function it_should_generate_correct_sql_for_diff_fetcher_type() {
        $columnOperation = new ColumnOperation(Fetcher::TYPE_DIFF, 'columnB');
        $operationValue = $columnOperation->getOperationValue('columnA');

        $this->assertNotEmpty($operationValue);

        $this->assertEquals('columnA != columnB', $operationValue[0]);
        $this->assertEmpty($operationValue[1]);
    }
}
