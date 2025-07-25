<?php

namespace Drupal\voteapp\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\field_ui\Form\EntitySettingsForm;

/**
 * Configure Question settings for this site.
 */
class QuestionSettingsForm extends EntitySettingsForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'question_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['voteapp.settings'];
  }
}
