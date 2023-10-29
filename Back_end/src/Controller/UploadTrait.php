<?php

namespace App\Controller;

trait UploadTrait
{
    public function handleUploadedImage($uploadDirectory)
    {
        // Assurez-vous que le répertoire d'uploads existe et a les bonnes autorisations
        if (!file_exists($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
        }

        // Traitement du fichier
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_FILES['portrait'])) {
                $file = $_FILES['portrait'];

                // Vérifiez s'il n'y a pas d'erreurs lors de l'upload
                if ($file['error'] === UPLOAD_ERR_OK) {
                    $fileName = basename($file['name']);
                    $uploadPath = $uploadDirectory . $fileName;

                    // Déplacez le fichier téléchargé vers le répertoire d'uploads
                    move_uploaded_file($file['tmp_name'], $uploadPath);

                    // Vous pouvez faire plus de traitement ici si nécessaire

                    // Répondez avec un message de succès
                    echo 'Image uploaded successfully';
                } else {
                    // Répondez avec un message d'erreur
                    echo 'Error uploading image';
                }
            } else {
                // Répondez avec un message d'erreur
                echo 'No image file provided';
            }
        } else {
            // Répondez avec un message d'erreur pour les requêtes non-POST
            echo 'Invalid request method';
        }
    }
}


// class ExampleClass
// {
//     use UploadTrait;

//     public function exampleMethod()
//     {
//         $uploadDirectory = 'uploads/';
//         $this->handleUploadedImage($uploadDirectory);
//     }
// }

// // Exemple d'utilisation
// $exampleObject = new ExampleClass();
// $exampleObject->exampleMethod();
