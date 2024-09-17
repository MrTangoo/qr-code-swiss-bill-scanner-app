<!-- Versionning -->
<!--
    Version     : '1.0.0'
    Date        : '04.09.24'
    Auteur      : 'GRI/MDE'
    Description : 'On récupère le ficher, le transforme en image et on lit son QR Code pour recevoir les informations.'
-->
<!--
ChangeLog  04.09.24 | 1.0.0 MDE : Adaptation du code pour php vanilla
ChangeLog  12.09.24 | 1.0.1 MDE : Adaptation du code pour les tests unitaire
ChangeLog  12.09.24 | 1.0.2 MDE : Modification des noms de variables
-->

<?php

require "../vendor/autoload.php";

use Zxing\QrReader;

function fileUpload(): string|false
{
    // Vérifier si un fichier a été téléchargé
    if (!isset($_FILES["pdfFile"])) {
        redirectWithError("Aucun fichier sélectionné.");
        return false;
    }

    $arrFile = $_FILES["pdfFile"];
    return handleFileUpload($arrFile);
}

function handleFileUpload(array $arrFile): string|false
{
    // Vérifier les erreurs de téléchargement
    if ($arrFile["error"] !== UPLOAD_ERR_OK) {
        redirectWithError("Erreur lors du téléchargement du fichier.");
        return false;
    }

    // Définir le chemin pour enregistrer le PDF
    $strPdfFilePath = "../uploads/" . basename($arrFile["name"]);

    // Déplacer le fichier téléchargé vers le répertoire "uploads"
    if (!move_uploaded_file($arrFile["tmp_name"], $strPdfFilePath)) {
        redirectWithError("Erreur lors du déplacement du fichier téléchargé.");
        return false;
    }

    return $strPdfFilePath;
}

function convertPdfToImage(string $strPdfFilePath): string|false
{
    // Définir le chemin pour enregistrer l'image JPG
    $strImagePath = "../uploads/output_image.jpg";

    try {
        // Utiliser Imagick pour convertir le PDF en image JPG
        $objImagick = new Imagick();
        $objImagick->setResolution(300, 300);
        $objImagick->readImage($strPdfFilePath . "[0]"); // Lit la première page du PDF
        $objImagick->setImageFormat("jpg");
        $objImagick->writeImage($strImagePath);
    } catch (Exception $objException) {
        redirectWithError(
            "Erreur lors de la conversion du PDF en image : " . $objException->getMessage()
        );
        return false;
    }

    return $strImagePath;
}

function readQrCodeFromImage(string $strImagePath): string|false
{
    // Lire le code QR à partir de l'image
    $objQrCode = new QrReader($strImagePath);
    $strText = $objQrCode->text();

    // Vérifier si un code QR a été détecté
    if (!$strText) {
        redirectWithError("Aucun code QR détecté.");
        return false;
    }

    return $strText;
}

function extractDataFromQrCode(string $strText): array
{
    $arrLines = explode("\n", $strText);
    $arrLines = array_filter($arrLines, fn($strLine) => trim($strLine) !== "");

    // Extraire les données du QR code
    return [
        "strIban" => $arrLines[3] ?? "Non trouvé",
        "strSupplier" => $arrLines[5] ?? "Non trouvé",
        "strTotalTtc" => $arrLines[18] ?? "Non trouvé",
        "strCash" => $arrLines[19] ?? "Non trouvé",
    ];
}

function redirectWithError(string $strMessage): void
{
    header("Location: ../index.php?result=" . urlencode($strMessage));
    exit();
}

function redirectWithData(array $arrData, string $strPdfFilePath): void
{
    $strPdfUrl = "uploads/" . basename($strPdfFilePath);
    $strPdfName = basename($strPdfFilePath);

    header(
        "Location: ../index.php?info=1&iban=" .
            urlencode($arrData["strIban"]) .
            "&supplier=" .
            urlencode($arrData["strSupplier"]) .
            "&totalTtc=" .
            urlencode($arrData["strTotalTtc"]) .
            "&cash=" .
            urlencode($arrData["strCash"]) .
            "&pdfUrl=" .
            urlencode($strPdfUrl) .
            "&pdfName=" .
            urlencode($strPdfName)
    );
    exit();
}

// Appel des fonctions
$strPdfFilePath = fileUpload();
if (!$strPdfFilePath) {
    exit();
}

$strImagePath = convertPdfToImage($strPdfFilePath);
if (!$strImagePath) {
    exit();
}

$strQrCodeText = readQrCodeFromImage($strImagePath);
if (!$strQrCodeText) {
    exit();
}

$arrData = extractDataFromQrCode($strQrCodeText);
redirectWithData($arrData, $strPdfFilePath);
