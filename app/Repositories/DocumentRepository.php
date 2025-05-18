<?php

namespace App\Repositories;

use App\Models\Document;

class DocumentRepository
{
    /**
     * Create a new document.
     *
     * @param array $data
     * @return \App\Models\Document
     */
    public function create(array $data)
    {
        return Document::create($data);
    }
}
