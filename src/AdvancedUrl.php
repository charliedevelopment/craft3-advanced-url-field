<?php

namespace charliedevelopment\advancedurl;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Fields;

use charliedevelopment\advancedurl\fields\AdvancedUrlField;

use yii\base\Event;

class AdvancedUrl extends Plugin
{
	public function init()
	{
		parent::init();
		
		Event::on(Fields::class, Fields::EVENT_REGISTER_FIELD_TYPES, [$this, 'registerFieldTypes']);
	}

	public function registerFieldTypes(RegisterComponentTypesEvent $event)
	{
		$event->types[] = AdvancedUrlField::class;
	}
}