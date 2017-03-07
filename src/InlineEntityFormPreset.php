<?php

namespace Drupal\inline_entity_form_preset;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Field\EntityReferenceFieldItemListInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\WidgetInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\inline_entity_form\Plugin\Field\FieldWidget\InlineEntityFormComplex;

/**
 * Class InlineEntityFormPreset
 */
class InlineEntityFormPreset {

  /**
   * Alters the summary of widget settings form.
   *
   * @param array $summary
   * @param array $context
   */
  public static function alterWidgetSettingsSummary(&$summary, $context) {
    /** @var \Drupal\Core\Field\WidgetInterface $widget */
    $widget = $context['widget'];

    if ($widget->getPluginId() == 'inline_entity_form_complex') {
      if ($status = $widget->getThirdPartySetting('inline_entity_form_preset', 'allow_existing_preset')) {
        // @todo Add reference to referenced entity type (singular and plural).
        $summary[] = t('Existing references can be referenced from a preset.');
      }
    }
  }

  /**
   * Alter widget settings form.
   *
   * @param \Drupal\Core\Field\WidgetInterface $plugin
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   * @param $form_mode
   * @param $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array
   */
  public static function widgetSettingsForm(WidgetInterface $plugin, FieldDefinitionInterface $field_definition, $form_mode, $form, FormStateInterface $form_state) {
    $element = [];

    if ($plugin->getPluginId() == 'inline_entity_form_complex') {
      $element['allow_existing_preset'] = [
        '#type' => 'checkbox',
        '#title' => t('Allow users to add existing references from a preset.'),
        '#default_value' => $plugin->getThirdPartySetting('inline_entity_form_preset', 'allow_existing_preset'),
      ];
    }

    return $element;
  }

  /**
   * Adds custom submit handler to entity form display form.
   *
   * @param $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public static function alterEntityFormDisplayForm(&$form, FormStateInterface $form_state) {
    array_unshift($form['actions']['submit']['#submit'], [get_called_class(), 'entityFormDisplayFormSubmit']);
  }

  /**
   * Removes values from a display if adding from a preset is not allowed.
   *
   * @param $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public static function entityFormDisplayFormSubmit($form, FormStateInterface $form_state) {
    foreach ($form_state->getValue('fields') as $field_name => $value) {
      if (!empty($value['type']) && $value['type'] == 'inline_entity_form_complex') {
        /** @var \Drupal\Core\Entity\EntityFormInterface $entity_form */
        $entity_form = $form_state->getFormObject();
        /** @var \Drupal\Core\Entity\Display\EntityFormDisplayInterface $display */
        $display = $entity_form->getEntity();

        // Get display component.
        if ($display_component = $display->getComponent($field_name)) {
          // Check if adding existing references from a preset is allowed.
          if (empty($display_component['third_party_settings']['inline_entity_form_preset']['allow_existing_preset'])) {
            // Unset the module settings if adding existing references from a preset is not allowed.
            unset($display_component['third_party_settings']['inline_entity_form_preset']);
            $display->setComponent($field_name, $display_component);
          }
        }
      }
    }
  }

  /**
   * Alters widget form and adds preset selection field.
   *
   * @param $element
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @param $context
   */
  public static function alterWidgetForm(&$element, FormStateInterface $form_state, $context) {
    /** @var InlineEntityFormComplex $widget */
    $widget = $context['widget'];

    if ($widget->getThirdPartySetting('inline_entity_form_preset', 'allow_existing_preset')) {
      $ief_id = $element['#ief_id'];
      /** @var EntityReferenceFieldItemListInterface $items */
      $items = $context['items'];
      $wrapper = 'inline-entity-form-' . $ief_id;

      $parents = array_merge($element['#field_parents'], [
        $items->getName(),
        'form',
      ]);

      // If no form is open, show buttons that open one.
      $open_form = $form_state->get(['inline_entity_form', $ief_id, 'form']);

      if (empty($open_form)) {
        $element['actions']['ief_add_existing_from_preset'] = [
          '#type' => 'submit',
          '#value' => t('Add existing from a preset'),
          '#ajax' => [
            'callback' => 'inline_entity_form_get_element',
            'wrapper' => $wrapper,
          ],
          '#submit' => ['inline_entity_form_open_form'],
          '#ief_form' => 'ief_add_existing_from_preset',
        ];
      }
      else {
        if ($form_state->get(['inline_entity_form', $ief_id, 'form']) == 'ief_add_existing_from_preset') {
          $target_type = $items->getFieldDefinition()->getTargetEntityTypeId();
          /** @var \Drupal\Core\Entity\EntityTypeInterface $target_type_definition */
          $target_type_definition = \Drupal::service('entity_type.manager')->getDefinition($target_type);
          $labels = [
            'singular' => \Drupal::service('entity_type.manager')->getDefinition($target_type)->getSingularLabel(),
          ];

          $element['form'] = [
            '#type' => 'fieldset',
            '#attributes' => ['class' => ['ief-form', 'ief-form-bottom']],
            '#ief_id' => $ief_id,
            '#parents' => $parents,
            '#entity_type' => $target_type,
            '#field_name' => $items->getName(),
            '#title' => t('Add existing from a preset'),
            '#ief_element_submit' => [[get_called_class(), 'inlineEntityFormWidgetSubmit']],
            '#ief_labels' => $labels,
          ];

          // Check if target entity type has bundles.
          if ($target_type_definition->hasKey('bundle')) {
            // If entity type has bundles, select only the bundle of a parent
            // entity.
            $target_bundle = $items->getParent()->getValue()->bundle();
            $target_bundles = [$target_bundle => $target_bundle];
          }
          else {
            $target_bundles = NULL;
          }

          $element['form'] += inline_entity_form_reference_form($element['form'], $form_state);
          $element['form']['actions']['ief_reference_save']['#value'] = t('Add references from @type_singular', ['@type_singular' => $labels['singular']]);
          $element['form']['entity_id']['#target_type'] = $target_type;
          $element['form']['entity_id']['#selection_handler'] = 'default:' . $target_type;
          $element['form']['entity_id']['#selection_settings']['target_bundles'] = $target_bundles;
          $element['form']['entity_id']['#selection_settings']['auto_create'] = FALSE;
          $element['form']['entity_id']['#selection_settings']['auto_create_bundle'] = NULL;
        }
      }
    }
  }

  /**
   * Adds references from a preset to the entity.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public static function inlineEntityFormWidgetSubmit(&$form, FormStateInterface $form_state) {
    try {
      $form_values = NestedArray::getValue($form_state->getValues(), $form['#parents']);
      $preset_storage = \Drupal::entityTypeManager()->getStorage($form['#entity_type']);

      if ($preset_entity = $preset_storage->load($form_values['entity_id'])) {
        $field_name = $form['#field_name'];

        // Get referenced entities from the preset.
        if ($preset_referenced_entities = $preset_entity->get($field_name)->referencedEntities()) {
          $ief_id = $form['#ief_id'];
          $entities = &$form_state->get(['inline_entity_form', $ief_id, 'entities']);

          // Determine the correct weight of the starting element.
          $weight = 0;
          if ($entities) {
            $weight = max(array_keys($entities)) + 1;
          }

          foreach ($preset_referenced_entities as $preset_referenced_entity) {
            $entities[] = [
              'entity' => $preset_referenced_entity,
              'weight' => $weight,
              'form' => NULL,
              'needs_save' => FALSE,
            ];
            $weight++;
          }

          $form_state->set(['inline_entity_form', $ief_id, 'entities'], $entities);
        }
      }
    }
    catch (\Exception $e) {
      watchdog_exception('inline_entity_form_preset', $e);
    }
  }

}
