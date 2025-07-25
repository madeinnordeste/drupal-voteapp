<?php

namespace Drupal\voteapp\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the Answer entity.
 *
 * @ContentEntityType(
 *   id = "answer",
 *   label = @Translation("Answer"),
 *   base_table = "answer",
 *   entity_keys = {
 *     "id" = "id"
 *   },
 *   handlers = {
 *     "form" = {
 *       "default" = "Drupal\voteapp\Form\AnswerForm",
 *       "add" = "Drupal\voteapp\Form\AnswerForm",
 *       "edit" = "Drupal\voteapp\Form\AnswerForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "list_builder" = "Drupal\voteapp\AnswerListBuilder",
 *   },
 *   admin_permission = "administer question entities",
 *   links = {
 *     "add-form" = "/admin/content/answer/add",
 *     "edit-form" = "/admin/content/answer/{answer}/edit",
 *     "delete-form" = "/admin/content/answer/{answer}/delete",
 *     "collection" = "/admin/content/answer"
 *   },
 *   field_ui_base_route = "entity.answer.settings",
 *   translatable = FALSE,
 *   show_revision_ui = FALSE
 * )
 */
//class Answer extends ContentEntityBase implements EntityPublishedInterface
class Answer extends ContentEntityBase
{

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
  {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Question reference
    $fields['question'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Question'))
      ->setDescription(t('The question this answer belongs to.'))
      ->setSetting('target_type', 'question')
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // Title
    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setRequired(TRUE)
      ->setSettings([
        'max_length' => 255,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // Image
    $fields['image'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Image'))
      ->setDescription(t('Optional image for this answer.'))
      ->setDisplayOptions('form', [
        'type' => 'image_image',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // Description
    $fields['description'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Description'))
      ->setDisplayOptions('form', [
        'type' => 'string_textarea',
        'weight' => 4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);


    return $fields;
  }


  public function getImageURL()
  {

    $imageURL = null;

    if (!$this->get('image')->isEmpty()) {
      $imageURL = \Drupal::service('stream_wrapper_manager')->getViaUri(
        $this->get('image')->entity->getFileUri()
      )->getExternalUrl();
    }

    return $imageURL;
  }
}
