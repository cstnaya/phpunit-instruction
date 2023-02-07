<?php

use PHPUnit\Framework\TestCase;

use Src\Models\PostModel;
use Src\tests\ReflectionHelper;

class PostModelTest extends TestCase
{
    /**
     * Execute before each test function
     */
    public function setUp(): void
    {
        $this->model = new PostModel();
    }

    public function test_insert()
    {
        // arrange
        $postData = (object) [
            'title' => "The First Post",
            'content' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s"
        ];

        // act
        $result = $this->model->insert($postData);
    
        // assert
        $this->assertNotNull($result->id);
        $this->assertEquals($postData->title, $result->title);
        $this->assertEquals($postData->content, $result->content);
    }

    public function test_show()
    {
        // arrange
        $mockData = (object) [
            'title' => "The First Post",
            'content' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s"
        ];
        $this->model->insert($mockData);

        // act
        $result = $this->model->get();

        // assert
        foreach($result as $idx => $post) {
            $this->assertEquals($idx, $post->id);
        }
    }
}
