<?php 

namespace Src\Models;

class PostModel
{
    // DB, we use an array here instead.
    private $data = [];

    public function __construct()
    {
        // dummy data
        $this->data = [
            (object) ["id" => 0, "title" => "Cookie Recipe", "content" => "Watch this video to learn baking cookies in 10 mins!"],
            (object) ["id" => 1, "title" => "2002 Health Exam Result", "content" => "I was diagnosed with bleeding cancer..."],
        ];
    }

    public function insert($newPost)
    {
        // append id in data
        $newPost->id = count($this->data);

        // insert into db
        $this->data[] = $newPost;

        // generally, we return the data what we just inserted in Create/Post method.
        return $newPost;
    }

    public function get()
    {
        return $this->data;
    }
}
