<?php declare(strict_types = 1);

namespace Drupal\custom_read_more\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'read more' formatter.
 *
 * @FieldFormatter(
 *   id = "custom_read_more",
 *   label = @Translation("read more"),
 *   field_types = {
 *    "text",
 *    "string_long",
 *  },
 * )
 */
final class ReadMoreFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    $setting = [
      'format' => 'word',
      'trim_length' => 50,
    ];
    return $setting + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $elements['format'] = [
      '#type' => 'radios',
      '#title' => $this->t('crop by: '),
      '#options' => [
        'word' => $this->t('By word'),
        'character' => $this->t('By character'),
      ],
      '#default_value' => $this->getSetting('format') ?? 'word',
    ];

    $elements['trim_length'] = [
      '#type' => 'number',
      '#title' => $this->t('trim length'),
      '#default_value' => $this->getSetting('trim_length') ?? 50,
    ];
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    return [
      $this->t('set read more text field by @trim_length @format ', [
          '@trim_length' => $this->getSetting('trim_length'),
          '@format' => $this->getSetting('format'),
        ]),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = [];
    foreach ($items as $delta => $item) {
      $trimmed = $this->trimText($item->value, $this->getSetting('format'), (int)$this->getSetting('trim_length'));
      $element[$delta] = [
        '#theme' => 'read_more',
        '#text' => $item->value,
        '#trimmed' => $trimmed,
        '#attached' => [
          'library' => 'custom_read_more/default',
        ],
      ];
    }
    return $element;
  }

  /**
   * Trims text
   * 
   * @param string $text
   *   Text string.
   * @param string $format
   *   Trim format word or characters.
   * @param int $length
   *   Trimming length.
   * 
   * @return string
   *  return trimmed string.
   */
  protected function trimText(string $text, string $format, int $length) {
    // trimming text.
    if ($format == 'word') {
      $words = str_word_count($text, 1);
      $slicedWord = array_slice($words, 0, $length);
      return implode(" ", $slicedWord) . "...";
    }
    return substr($text, 0, $length) . "...";
  }

}
