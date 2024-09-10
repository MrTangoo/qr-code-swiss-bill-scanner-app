<?php

use PHPUnit\Framework\TestCase;

class ProcessTest extends TestCase
{
    protected function setUp(): void
    {
        $_FILES = [
            'pdfFile' => [
                'name' => 'test.pdf',
                'tmp_name' => '/tmp/php/phpf1234',
                'error' => UPLOAD_ERR_OK,
                'size' => 1234,
                'type' => 'application/pdf'
            ]
        ];
    }

    public function testHandleFileUploadSuccess() {}
}
