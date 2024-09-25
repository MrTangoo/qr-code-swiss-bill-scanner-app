<?php

use PHPUnit\Framework\TestCase;
use Zxing\QrReader;




class ProcessTest extends TestCase
{

    private $strPdfPath;

    private $strOutputImagePath;

    // Méthode exécutée avant chaque test
    protected function setUp(): void
    {
        // Définit le chemin du fichier PDF à convertir
        $this->strPdfPath = 'sample/test.pdf';

        // Définit le chemin de sortie pour l'image générée
        $this->strOutputImagePath = '../uploads/output_image.jpg';
    }

    // Test pour vérifier si la conversion du PDF en image est réussie
    public function testConvertPdfToImageSuccessful()
    {
        $strResult = fnConvertPdfToImage($this->strPdfPath);

        $this->assertEquals($strResult, $this->strOutputImagePath);
        $this->assertFileExists($this->strOutputImagePath);
    }

    // Test pour vérifier si la lecture du code QR dans l'image est réussie
    public function testReadQrCodeFromImageSuccessful()
    {
        $strResult = fnReadQrCodeFromImage($this->strOutputImagePath);
        $this->assertNotFalse($strResult);
        $this->assertNotEmpty($strResult);

        if (file_exists($this->strOutputImagePath)) {
            unlink($this->strOutputImagePath);
        }
    }
}

function fnConvertPdfToImage($strPdfFilePath)
{
    // Définir le chemin pour enregistrer l'image JPG
    $strImagePath = "../uploads/output_image.jpg";

    try {
        // Utiliser Imagick pour convertir le PDF en image JPG
        $oImagick = new Imagick();
        $oImagick->setResolution(300, 300);
        $oImagick->readImage($strPdfFilePath . "[0]"); // Lit la première page du PDF
        $oImagick->setImageFormat("jpg");
        $oImagick->writeImage($strImagePath);
    } catch (Exception $e) {
        return false;
    }

    return $strImagePath;
}

function fnReadQrCodeFromImage($strImagePath)
{
    // Lire le code QR à partir de l'image
    $oQrcode = new QrReader($strImagePath);
    $strText = $oQrcode->text();

    // Vérifier si un code QR a été détecté
    if (!$strText) {
        fnRedirectWithError("Aucun code QR détecté.");
        return false;
    }

    return $strText;
}
