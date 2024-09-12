<?php

use PHPUnit\Framework\TestCase;
use Zxing\QrReader;


class ProcessTest extends TestCase
{

    private $pdfPath;

    private $outputImagePath;

    protected function setUp(): void
    {
        $this->pdfPath = __DIR__ . '/sample/test.pdf';

        $this->outputImagePath = '../uploads/output_image.jpg';
    }

    public function testConvertPdfToImageSuccessful()
    {
        $result = convertPdfToImage($this->pdfPath);

        $this->assertEquals($result, $this->outputImagePath);
        $this->assertFileExists($this->outputImagePath);
    }

    public function testReadQrCodeFromImageSuccessful()
    {
        $result = readQrCodeFromImage($this->outputImagePath);
        $this->assertNotFalse($result);
        $this->assertNotEmpty($result);
    }

    public function delete()
    {
        if (file_exists($this->outputImagePath)) {
            unlink($this->outputImagePath);
        }
    }
}


function convertPdfToImage($pdfFilePath)
{
    // Définir le chemin pour enregistrer l'image JPG
    $imagePath = "../uploads/output_image.jpg";

    try {
        // Utiliser Imagick pour convertir le PDF en image JPG
        $imagick = new Imagick();
        $imagick->setResolution(300, 300);
        $imagick->readImage($pdfFilePath . "[0]"); // Lit la première page du PDF
        $imagick->setImageFormat("jpg");
        $imagick->writeImage($imagePath);
    } catch (Exception $e) {
        return false;
    }

    return $imagePath;
}

function readQrCodeFromImage($imagePath)
{
    // Lire le code QR à partir de l'image
    $qrcode = new QrReader($imagePath);
    $text = $qrcode->text();

    // Vérifier si un code QR a été détecté
    if (!$text) {
        redirectWithError("Aucun code QR détecté.");
        return false;
    }

    return $text;
}
