<?php
require_once '../utilities/subscriberClass.php';

class CrunchyrollSubscriber extends Subscriber {

    public function __construct() {
        parent::__construct();
    }

    public function process_new_feed_content() {
        // Capturo el contenido del body
        // y lo guardo en el output_file
        $this->set_body_content();
        $this->save_body_content();
                
        // Convierto el contenido del body de XML a JSON
        // y lo guardo en el output_file del conversor
        require_once('./crunchyrollConversor.php');
        $this->conversor = new CrunchyrollConversor($this->get_body_content());
        $this->conversor->convert_to_json();
        $this->conversor->save_json();
        
        // Subo el JSON a Firestore
        require_once('./crunchyrollFirestoreManager.php');
        $this->firestore_manager = new CrunchyrollFirestoreManager();
        $this->firestore_manager->upload_data($this->conversor->get_array());

        // Notifico a los usuarios vía email
        require_once('./crunchyrollNotifier.php');
        $this->notifier = new CrunchyrollNotifier();
        $this->notifier->notify_subscribers($this->conversor->get_array(), $this->firestore_manager);
        
        $this->return_ok();
    }

}

$subscriber = new CrunchyrollSubscriber();
$subscriber->process_request();