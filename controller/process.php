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

function fnFileUpload(): string|false
{
    // Vérifier si un fichier a été téléchargé
    if (!isset($_FILES["pdfFile"])) {
        fnRedirectWithError("Aucun fichier sélectionné.");
        return false;
    }

    $arrFile = $_FILES["pdfFile"];
    return fnHandleFileUpload($arrFile);
}

function fnHandleFileUpload(array $arrFile): string|false
{
    // Vérifier les erreurs de téléchargement
    if ($arrFile["error"] !== UPLOAD_ERR_OK) {
        fnRedirectWithError("Erreur lors du téléchargement du fichier.");
        return false;
    }

    // Définir le chemin pour enregistrer le PDF
    $strPdfFilePath = "../uploads/" . basename($arrFile["name"]);

    // Déplacer le fichier téléchargé vers le répertoire "uploads"
    if (!move_uploaded_file($arrFile["tmp_name"], $strPdfFilePath)) {
        fnRedirectWithError("Erreur lors du déplacement du fichier téléchargé.");
        return false;
    }

    return $strPdfFilePath;
}

function fnConvertPdfToImage(string $strPdfFilePath): string|false
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
    } catch (Exception $oException) {
        fnRedirectWithError(
            "Erreur lors de la conversion du PDF en image : " . $oException->getMessage()
        );
        return false;
    }

    return $strImagePath;
}

function fnReadQrCodeFromImage(string $strImagePath): string|false
{
    // Lire le code QR à partir de l'image
    $oQrCode = new Libern\QRCodeReader\QRCodeReader();
    $strText = $oQrCode->decode($strImagePath);

    // Vérifier si un code QR a été détecté
    if (!$strText) {
        fnRedirectWithError("Aucun code QR détecté.");
        return false;
    }

    return $strText;
}

function fnExtractDataFromQrCode(string $strText): array
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

function fnRedirectWithError(string $strMessage): void
{
    header("Location: ../index.php?result=" . urlencode($strMessage));
    exit();
}

function fnRedirectWithData(array $arrData, string $strPdfFilePath): void
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
$strPdfFilePath = fnFileUpload();
if (!$strPdfFilePath) {
    exit();
}

$strImagePath = fnConvertPdfToImage($strPdfFilePath);
if (!$strImagePath) {
    exit();
}

$strQrCodeText = fnReadQrCodeFromImage($strImagePath);
if (!$strQrCodeText) {
    exit();
}

$arrData = fnExtractDataFromQrCode($strQrCodeText);
fnRedirectWithData($arrData, $strPdfFilePath);
