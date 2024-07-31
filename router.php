<?php

$bot = new Bot();
$task = new Task();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['text'])) {
    $task->add_add($_POST['text']);
  }

  header('Location view/view.php');
  return;

}

if (isset($_GET['update'])) {
  if (false){
    $task->uncompleted($_GET['update']);

  } else {
    $task->complete($_GET['update']);

  }
  header('Location view/view.php');
  return;

}  

if (isset($_GET['delete'])) {
  
  $task->delete($_GET['delete']);

  header('Location view/view.php');
  return;
}

require 'view/view.php';
?>