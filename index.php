<!-- Versionning -->
<!--
    Page        : 'Page upload'
    Version     : '1.0.0'
    Date        : '04.09.24'
    Auteur      : 'GRI/MDE'
    Description : 'Page où on sélectionne la facture et les infos s'affiche'
-->
<!--
ChangeLog  04.09.24 | 1.0.0 MDE : Adaptation du code pour php vanilla
-->

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="shortcut icon" href="#">
    <link href="./css/style.css" rel="stylesheet">
    <title>Lecture de Code QR</title>
</head>

<body>
    <div id="spinner" class="spinner"></div>
    <header>
        <nav>
            <div class="logo">
                <a href="/">
                    <h1>GRI</h1>
                </a>
            </div>
        </nav>
    </header>
    <div class="main-container">
        <div class="title">
            <h1>Facture</h1>
        </div>
        <div class="main-grid-container">
            <div class="main-content">
                <form id="uploadForm" action="./controller/process.php" method="POST" enctype="multipart/form-data">
                    <label for="fileInput" class="custom-file-upload">Sélectionnez une facture</label>
                    <input type="file" name="pdfFile" id="fileInput" accept="application/pdf" required>
                    <?php if (isset($_GET["info"])): ?>
                        <label id="file-name"><?= htmlspecialchars($_GET["pdfName"]) ?></label>
                    <?php endif; ?>
                    <div id="drop-area"> <i class="fa fa-upload fa-lg" aria-hidden="true"></i> <br> Ou déposer le fichier ici </div>
                </form>

                <?php if (isset($_GET["result"])): ?>
                    <p id="result"> <?= htmlspecialchars($_GET["result"]) ?> </p>
                <?php endif; ?>

                <?php if (isset($_GET["info"])): ?>
                    <div class="main-grid-container">
                        <div class="input-container">
                            <div class="grid-container">
                                <div class="first-column">
                                    <label for="iban">Iban</label>
                                    <br>
                                    <input id="iban" placeholder="<?= htmlspecialchars($_GET["iban"]) ?>">
                                    <br>
                                    <label for="total-ttc">Total TTC</label>
                                    <br>
                                    <input id="total-ttc" placeholder="<?= htmlspecialchars($_GET["totalTtc"]) ?>">
                                    <br>
                                </div>
                                <div class="second-column">
                                    <label for="supplier">Fournisseur</label>
                                    <br>
                                    <input id="supplier" placeholder="<?= htmlspecialchars($_GET["supplier"]) ?>">
                                    <br>
                                    <label for="cash">Monnaie</label>
                                    <br>
                                    <input id="cash" placeholder="<?= htmlspecialchars($_GET["cash"]) ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn">
                        <a href="/">
                            <button id="save-btn">Enregistrer</button>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (isset($_GET["info"])): ?>
                <div class="display-pdf">
                    <iframe src="<?= htmlspecialchars($_GET["pdfUrl"]) ?>" width="100%" height="630px"></iframe>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="./js/script.js"></script>
</body>

</html>