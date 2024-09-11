<?php

use PHPUnit\Framework\TestCase;

require("process.php");

class ProcessTest extends TestCase
{
    protected function setUp(): void
    {
        if (!function_exists('move_uploaded_file')) {
            function move_uploaded_file($source, $destination)
            {
                return true;
            }
        }
    }

    public function testHandleFileUploadSuccessful()
    {
        /*
        $file = [
            "name" => "test.pdf",
            "type" => "application/pdf",
            "tmp_name" => "/tmp/phpYzdqkD",
            "error" => UPLOAD_ERR_OK,
            "size" => 123456
        ];*/

        // require_once '../qr-code-scanner-app/controller/process.php';

        $file = handleFileUpload("test.pdf");

        // $this->assertTrue(move_uploaded_file($file['tmp_name'], "../uploads/" . basename($file['name'])));
        // $result = handleFileUpload($file);
        // $this->assertEquals("../uploads/test.pdf", $result);

        $this->assertEquals($file, false);
        $this->assertNotEmpty($file);
    }
}
