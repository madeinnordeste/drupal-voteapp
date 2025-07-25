<?php


$definitions = \Drupal::entityTypeManager()->getDefinitions();
foreach ($definitions as $id => $definition) {
  echo $id . " => " . $definition->getLabel() . PHP_EOL;
}
