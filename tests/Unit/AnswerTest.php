<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Answer;

class AnswerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    private $answer;
    
    protected function setUp(): void {
        $this->answer = new Answer();
    }

    /**
     * @dataProvider basicProvider
     */
    public function testBasicTest($typeRequest,  $data, $next, $forse, $expected)
    {
        $this->assertSame(
            $expected,
            $this->answer->returnResponse($typeRequest, $data, $next, $forse)
            );
    }
    
    public function basicProvider() {
        return [
            // GET cases
            ['get', '', '', false, ['get', '', '', false, false, 404]],
            ['get', ['test'], '', false, ['get', ['test'], '', false, true, 200]],
            ['get', 'test', '', false, ['get', 'test', '', false, true, 200]],
            ['get', true, '', false, ['get', true, '', false, true, 200]],
            ['get', false, '', false, ['get', false, '', false, false, 404]],
            ['get', '', '', true, ['get', '', '', true, true, 200]],
            //POST cases
            ['post', '', '', false, ['post', '', '', false, false, 400]],
            ['post', '', '', true, ['post', '', '', true, true, 201]],

        ];
    }
}
