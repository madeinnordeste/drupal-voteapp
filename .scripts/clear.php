<?php

$storage = \Drupal::entityTypeManager()->getStorage('question');
$entities = $storage->loadMultiple();
$storage->delete($entities);

$storage = \Drupal::entityTypeManager()->getStorage('answer');
$entities = $storage->loadMultiple();
$storage->delete($entities);


$storage = \Drupal::entityTypeManager()->getStorage('vote');
$entities = $storage->loadMultiple();
$storage->delete($entities);
