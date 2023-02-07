<?php

namespace Src\Controllers;

use Src\Models\PostModel;

class PostController
{
    private $model;

    public function __construct(PostModel $PostModel)
    {
        $this->model = $PostModel;
    }

    /**
     * Return all data fetched from model.
     */
    public function show()
    {
        return $this->model->get();
    }
}
