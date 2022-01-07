<?php
// References: https://firebase.google.com/docs/firestore/quickstart?hl=es

// 1
// 
// Entorno de desarrollo
//  Configura la variable de entorno GOOGLE_APPLICATION_CREDENTIALS
//  $_SERVER["GOOGLE_APPLICATION_CREDENTIALS"] = "path/to/your/keyfile.json";
// 
// Entorno de producción
//  Configura una cuenta de servicio.

// 2
// Instala y habilita la extensión de gRPC para PHP, que necesitarás para usar la biblioteca cliente.

// 3
// Agrega la biblioteca PHP de Cloud Firestore a tu app:

// Carga automáticamente las clases de Firestore requeridas 
// con el autoloader de composer 

//autoload de composer
require '../vendor/autoload.php';

// Inicializa una instancia de Cloud Firestore:
use Google\Cloud\Firestore\FirestoreClient;
$myUploader = new Uploader();
$myUploader->retrieve_subscribers();

class Uploader  {
    private $db;

    public function __construct() {
        $this->create_db_client("my-anime-499f8");
    }

    // Initialize Cloud Firestore
    private function create_db_client(string $projectId = null) {
        // Create the Cloud Firestore client
        if (empty($projectId)) {
            // The `projectId` parameter is optional and represents which project the
            // client will act on behalf of. If not supplied, the client falls back to
            // the default project inferred from the environment.
            $this->db = new FirestoreClient();
            printf('Created Cloud Firestore client with default project ID.' . PHP_EOL);

        } else {
            $this->db = new FirestoreClient([
                'projectId' => $projectId,
            ]);
            printf('Created Cloud Firestore client with project ID: %s' . PHP_EOL, $projectId);

        }
    }


    public function upload_data($array_episode) {
        $docRef = $this->db->collection('samples/php/users')->document('lovelace');
        $docRef->set($array_episode);
        printf('Added data to the lovelace document in the users collection.' . PHP_EOL);
    }

    public function retrieve_subscribers(){
        $usersRef = $this->db->collection('samples/php/users');
        $snapshot = $usersRef->documents();
        foreach ($snapshot as $user) {
            printf('User: %s' . PHP_EOL, $user->id());
            printf('First: %s' . PHP_EOL, $user['first']);

            if (!empty($user['middle'])) {
                printf('Middle: %s' . PHP_EOL, $user['middle']);

            }
            printf('Last: %s' . PHP_EOL, $user['last']);
            printf('Born: %d' . PHP_EOL, $user['born']);
            printf(PHP_EOL);

        }
        printf('Retrieved and printed out all documents from the users collection.' . PHP_EOL);
    }

}