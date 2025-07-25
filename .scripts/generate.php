<?php

$qtty = 100;

$storage = \Drupal::entityTypeManager()->getStorage('question');
for ($i = 0; $i < $qtty; $i++) {

  $identifier =  bin2hex(random_bytes(8));
  $title = 'Pergunta de teste ' . $i . '(' . $identifier . ')';

  $question = $storage->create([
    'title' => $title,
    'identifier' => $identifier,
    'show_results' => TRUE,
    'status' => 1,
  ]);
  $question->save();


  //answer
  $aQtty = rand(3, 8);
  $aStorage = \Drupal::entityTypeManager()->getStorage('answer');
  for ($z = 0; $z < $aQtty; $z++) {

    $answer = $aStorage->create([
      'title' => 'Resposta de teste ' . $z,
      //'image' => '',
      'description' => 'Description resposta de teste ' . $z,
      'question' => $question->id()
    ]);
    $answer->save();
  }
}
