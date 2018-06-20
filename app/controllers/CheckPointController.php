<?php

namespace Controllers;

use Core\Controllers\Controller;
use Core\Helpers\Validator;
use Core\Helpers\Notificator;
use Model\CheckPoint;
use Model\Files;

class CheckPointController extends Controller
{

  /**
   * Checkpoint method
   *
   * @return void
   */
  public function checkpoint()
  {

    // var_dump($_POST);
    // echo '<pre>';
    // var_dump($_FILES);
    // echo '</pre>';
    // die();

    if(!Validator::checkPosted($_POST, ['receiver', 'sender', 'copy']))
      Notificator::notify($this->twig, 'danger', 'Sorry, an error occurred');

    $receiver = Validator::sanitarize($_POST['receiver']);
    $sender   = Validator::sanitarize($_POST['sender']);
    $copy     = Validator::sanitarize($_POST['copy']);

    if(!Validator::checkBoolean($copy))
      Notificator::notify($this->twig, 'danger', 'Sorry, an error occurred');

    if(!Validator::checkEmpty($receiver))
      Notificator::notify($this->twig, 'danger', 'Please give an receiver email');

    if(!Validator::checkEmail($receiver))
      Notificator::notify($this->twig, 'danger', 'Receveir email is not valid');

    if(!Validator::checkEmpty($sender))
      Notificator::notify($this->twig, 'danger', 'Please give your email');

    if(!Validator::checkEmail($sender))
      Notificator::notify($this->twig, 'danger', 'Sender email is not valid');

    if (isset($_FILES['files'])) {

      $checkPoint = new CheckPoint();

      $checkPoint->sender_email   = $sender;
      $checkPoint->receiver_email = $receiver;
      $checkPoint->save();

      $files      = $_FILES['files']['name'];
      $nb         = count($files);
      $uploadsDir = './uploads/';

      for ($i = 0 ; $i < $nb ; $i++) {

        $file              = new Files();
        $file->filename    = $files[$i];
        $file->transfer_id = $checkPoint->id;
        $file->save();

        $uploadFile = $uploadsDir.$files[$i];

        if (!move_uploaded_file($_FILES['files']['tmp_name'][$i], $uploadFile))
          Notificator::notify($this->twig, 'danger', 'Sorry, an error occurred');

      }

      echo json_encode(['redirection' => true, 'id' => 'klmfkglm9898']);

      // echo $this->twig->render('result.html.twig', [
      //     'test' => 'ok'
      // ]);

    } else {

      Notificator::notify($this->twig, 'danger', 'Please choose file(s)');

    }

  }

}
