<?php

use PHPUnit\Framework\TestCase;

use Src\Controllers\PostController;
use Src\Models\PostModel;

class PostControllerTest extends TestCase
{
    /**
     * Execute before each test function
     */
    public function setUp(): void
    {
        // arrange: create dummy data
        $this->mockData = [
            (object) ["id" => 0, "title" => "testcase title 1", "content" => "test content 1"],
            (object) ["id" => 1, "title" => "testcase title 2", "content" => "test content 2"],
        ];

        // mock PostModel
        $mock = Mockery::mock(PostModel::class);
        $mock->shouldReceive('get')->once()->andReturn($this->mockData);

        $this->controller = new PostController($mock);
    }

    public function test_show()
    {
        // act
        $result = $this->controller->show();

        // assert
        foreach ($result as $idx => $post) {
            $this->assertEquals($this->mockData[$idx]->id, $post->id);
        }
    }

    /**
     * Executed after each test function
     */
    public function tearDown(): void
    {
        Mockery::close();
    }
}
