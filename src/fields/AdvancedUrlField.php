<?php
/**
 * Advanced URL Field plugin for Craft 3.0
 * @copyright Copyright Charlie Development
 */

namespace charliedev\advancedurl\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\base\PreviewableFieldInterface;

use yii\db\Schema;

/**
 * This field supports text that must match at least one of the configured URL types.
 */
class AdvancedUrlField extends Field implements PreviewableFieldInterface
{

	/**
	 * @var array The possible options for allowed url types.
	 */
	const ALLOWED_URL_TYPES = [
		'relative',
		'absolute',
		'mailto',
		'tel',
	];

	/**
	 * @var string|null The input's placeholder text.
	 */
	public $placeholder;

	/**
	 * @var array The set of url types allowed to be used within this field.
	 */
	public $urlTypes = [];

	/**
	 * @inheritdoc
	 * @see craft\base\ComponentInterface
	 */
	public static function displayName(): string
	{
		return \Craft::t('advanced-url-field', 'Advanced URL');
	}

	/**
	 * @inheritdoc
	 * @see craft\base\Field
	 */
	public static function hasContentColumn(): bool
	{
		return true;
	}

	/**
	 * @inheritdoc
	 * @see craft\base\Field
	 */
	public function getContentColumnType(): string
	{
		return Schema::TYPE_STRING;
	}

	/**
	 * @inheritdoc
	 * @see craft\base\SavableComponentInterface
	 */
	public function getSettingsHtml(): string
	{
		return Craft::$app->getView()->renderTemplate(
			'advanced-url-field/_settings',
			[
				'field' => $this,
			]
		);
	}

	/**
	 * @inheritdoc
	 * @see craft\base\Field
	 */
	public function rules(): array
	{
		$rules = parent::rules();
		
		$rules[] = [['urlTypes'], 'required'];
		$rules[] = [['urlTypes'], 'in', 'range' => self::ALLOWED_URL_TYPES, 'allowArray' => true];
		
		return $rules;
	}
	
	/**
	 * @inheritdoc
	 * @see craft\base\Field
	 */
	public function getInputHtml($value, ElementInterface $element = null): string
	{
		return Craft::$app->getView()->renderTemplate('advanced-url-field/_input', [
			'field' => $this,
			'value' => $value,
		]);
	}

	/**
	 * @inheritdoc
	 * @see craft\base\Field
	 */
	public function getElementValidationRules(): array
	{
		return [
			['validateUrl'],
		];
	}

	/**
	 * Ensures the URL provided matches the allowed URL types.
	 * @param ElementInterface $element The element with the value being validated.
	 * @return void
	 */
	public function validateUrl(ElementInterface $element)
	{
		$value = $element->getFieldValue($this->handle);

		// Make sure the value matches at least one of the allowed types.
		$matches = false;
		foreach ($this->urlTypes as $type) {
			switch ($type) {
				case 'relative':
					// Starts with a forward slash, and contains no whitespace.
					$matches = $matches | preg_match('/^\/\S*$/iu', $value);
					break;
				case 'absolute':
					// Regex by diegoperini, documented at https://mathiasbynens.be/demo/url-regex
					$matches = $matches | preg_match('/^(?:(?:https?|ftp):\/\/)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:\/[^\s]*)?$/iu', $value);
					break;
				case 'mailto':
					// Begins with 'mailto:' and contains at least one @ symbol.
					$matches = $matches | preg_match('/^mailto:.*@.*/u', $value);
					break;
				case 'tel':
					// Begins with 'tel:'.
					$matches = $matches | preg_match('/^tel:.*/u', $value);
					break;
				default:
					$element->addError($this->handle, Craft::t('advanced-url-field', 'Unknown URL type in field settings.'));
					break;
			}
		}

		if (!$matches) {
			$element->addError($this->handle, Craft::t('advanced-url-field', 'URL provided does not match the allowed formats.'));
		}
	}
}
