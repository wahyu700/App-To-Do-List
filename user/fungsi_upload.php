<?php

function handleFileUpload($file, $uploadDir, $allowedExtensions, $maxFileSize) {
    $response = ['error' => true, 'message' => null, 'filename' => null];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileType = mime_content_type($file['tmp_name']);
        $fileSize = $file['size'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        $allowedMimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
        ];

        if (!in_array($fileExtension, $allowedExtensions) || !in_array($fileType, $allowedMimeTypes)) {
            $response['message'] = "Tipe file yang diunggah tidak diizinkan.";
        } elseif ($fileSize > $maxFileSize) {
            $response['message'] = "Ukuran file terlalu besar. Maksimum " . ($maxFileSize / (1024 * 1024)) . "MB.";
        } else {
            // Pastikan direktori upload ada dan writable
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    $response['message'] = "Gagal membuat direktori upload.";
                    return $response;
                }
            } elseif (!is_writable($uploadDir)) {
                $response['message'] = "Direktori upload tidak writable.";
                return $response;
            }

            $filename = time() . '_' . basename($file['name']);
            $targetPath = $uploadDir . $filename;
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $response['error'] = false;
                $response['filename'] = $filename;
            } else {
                $response['message'] = "Terjadi kesalahan saat mengupload file.";
            }
        }
    } elseif ($file['error'] !== UPLOAD_ERR_NO_FILE) {
        $response['message'] = "Terjadi kesalahan saat mengupload file. Error code: " . $file['error'];
    } else {
        $response['error'] = false; // Tidak ada file diupload, bukan error
    }

    return $response;
}

?>