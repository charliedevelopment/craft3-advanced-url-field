<?php

namespace charliedevelopment\advancedurl\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\base\PreviewableFieldInterface;

use yii\db\Schema;

class AdvancedUrlField extends Field implements PreviewableFieldInterface
{
	/**
	 * @var string|null The input's placeholder text.
	 */
	public $placeholder;

	/**
	 * @var array The set of url types allowed to be used.
	 */
	public $urlTypes = [];

	public static function displayName(): string
	{
		return \Craft::t('advanced-url-field', 'Advanced Url');
	}

	public static function hasContentColumn(): bool
	{
		return true;
	}

	public function getContentColumnType(): string
	{
		return Schema::TYPE_STRING;
	}

	public function getSettingsHtml(): string
	{
		return Craft::$app->getView()->renderTemplate('advanced-url-field/_settings',
			[
				'field' => $this,
			]);
	}

	public function getInputHtml($value, ElementInterface $element = null): string
	{
		return Craft::$app->getView()->renderTemplate('advanced-url-field/_input', [
				'field' => $this,
				'value' => $value,
			]);
	}
}