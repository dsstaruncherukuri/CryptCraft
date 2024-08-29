function encryptText() {
    var textToEncrypt = document.getElementById('textToEncrypt').value.trim();
    var encryptionKey = document.getElementById('encryptionKey').value.trim();
    var numberOfLayers = parseInt(document.getElementById('numberOfLayers').value, 10);

    if (!textToEncrypt || !encryptionKey || isNaN(numberOfLayers) || numberOfLayers < 1) {
        alert('Please enter text, encryption key, and a valid number of decoy layers.');
        return;
    }

    var encryptedText = textToEncrypt;
    for (var i = 0; i < numberOfLayers; i++) {
        encryptedText = CryptoJS.AES.encrypt(encryptedText, generateRandomKey()).toString();
    }

    var encryptedResult = {
        encryptedText: encryptedText,
        correctLayer: numberOfLayers
    };

    document.getElementById('encryptedResult').innerHTML = '<strong>Encrypted Text:</strong><br>' + encryptedResult.encryptedText +
        '<br><br><strong>Correct Layer:</strong> ' + encryptedResult.correctLayer;
}

function decryptText() {
    var encryptedText = document.getElementById('textToDecrypt').value.trim();
    var decryptionKey = document.getElementById('decryptionKey').value.trim();
    var decoyLayer = parseInt(document.getElementById('decoyLayer').value, 10);

    if (!encryptedText || !decryptionKey || isNaN(decoyLayer) || decoyLayer < 1) {
        alert('Please enter encrypted text, decryption key, and a valid decoy layer.');
        return;
    }

    var decryptedText = encryptedText;
    for (var i = 0; i < decoyLayer; i++) {
        try {
            decryptedText = CryptoJS.AES.decrypt(decryptedText, generateRandomKey()).toString(CryptoJS.enc.Utf8);
        } catch (error) {
            document.getElementById('decryptedResult').innerHTML = '<div class="error">Decryption failed. Incorrect decryption key or decoy layer.</div>';
            return;
        }
    }

    document.getElementById('decryptedResult').innerHTML = '<strong>Decrypted Text:</strong><br>' + decryptedText;
}

function generateRandomKey() {
    return CryptoJS.lib.WordArray.random(16).toString();
}
