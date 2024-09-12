// Versionning
/*
    Version     : '1.0.0'
    Date        : '04.09.24'
    Auteur      : 'GRI/MDE'
    Description : 'Script pour le file input eventListener, drag & drop et le loading spinner'
*/
// ChangeLog  04.09.24 | 1.0.0 MDE : Adaptation du code pour php vanilla


document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('fileInput');
    const dropArea = document.getElementById('drop-area');
    const spinner = document.getElementById('spinner');

    fileInput.addEventListener('change', function () {
        if (fileInput.files.length > 0) {
            spinner.style.display = 'block';
            document.getElementById('uploadForm').submit();
        }
    });

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => dropArea.classList.add('hover'), false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => dropArea.classList.remove('hover'), false);
    });

    dropArea.addEventListener('drop', handleDrop, false);

    dropArea.addEventListener('click', () => fileInput.click(), false);

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length > 0) {
            fileInput.files = files;
            spinner.style.display = 'block';
            document.getElementById('uploadForm').submit();
        }
    }
});
