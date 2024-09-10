<!-- Versionning -->
<!--
    Version     : '1.0.0'
    Date        : '04.09.24'
    Auteur      : 'GRI/MDE'
    Description : 'On récupère le ficher, le transforme en image et on lit son QR Code pour recevoir les informations.'
-->
<!--
ChangeLog  04.09.24 | 1.0.0 MDE : Adaptation du code pour php vanilla
ChangeLog  10.09.24 | 1.0.1 MDE : Séparation en plusieurs fonction
-->

<?php

require "../vendor/autoload.php";

use Zxing\QrReader;

function handleFileUpload()
{
    // Vérifier si un fichier a été téléchargé
    if (!isset($_FILES["pdfFile"])) {
        redirectWithError("Aucun fichier sélectionné.");
        return false;
    }
    $file = $_FILES["pdfFile"];

    // Vérifier les erreurs de téléchargement
    if ($file["error"] !== UPLOAD_ERR_OK) {
        redirectWithError("Erreur lors du téléchargement du fichier.");
        return false;
    }

    // Définir le chemin pour enregistrer le PDF
    $pdfFilePath = "../uploads/" . basename($file["name"]);

    // Déplacer le fichier téléchargé vers le répertoire "uploads"
    if (!move_uploaded_file($file["tmp_name"], $pdfFilePath)) {
        redirectWithError("Erreur lors du déplacement du fichier téléchargé.");
        return false;
    }

    return $pdfFilePath;
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
        redirectWithError(
            "Erreur lors de la conversion du PDF en image : " . $e->getMessage()
        );
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

function extractDataFromQrCode($text)
{
    $lines = explode("\n", $text);
    $lines = array_filter($lines, fn($line) => trim($line) !== "");

    // Extraire les données du QR code
    return [
        "iban" => $lines[3] ?? "Non trouvé",
        "supplier" => $lines[5] ?? "Non trouvé",
        "totalTtc" => $lines[18] ?? "Non trouvé",
        "cash" => $lines[19] ?? "Non trouvé",
    ];
}

function redirectWithError($message)
{
    header("Location: ../index.php?result=" . urlencode($message));
    exit();
}

function redirectWithData($data, $pdfFilePath)
{
    $pdfUrl = "uploads/" . basename($pdfFilePath);
    $pdfName = basename($pdfFilePath);

    header(
        "Location: ../index.php?info=1&iban=" .
            urlencode($data["iban"]) .
            "&supplier=" .
            urlencode($data["supplier"]) .
            "&totalTtc=" .
            urlencode($data["totalTtc"]) .
            "&cash=" .
            urlencode($data["cash"]) .
            "&pdfUrl=" .
            urlencode($pdfUrl) .
            "&pdfName=" .
            urlencode($pdfName)
    );
    exit();
}

$pdfFilePath = handleFileUpload();
if (!$pdfFilePath) {
    exit();
}

$imagePath = convertPdfToImage($pdfFilePath);
if (!$imagePath) {
    exit();
}

$qrcodeText = readQrCodeFromImage($imagePath);
if (!$qrcodeText) {
    exit();
}

$data = extractDataFromQrCode($qrcodeText);
redirectWithData($data, $pdfFilePath);
