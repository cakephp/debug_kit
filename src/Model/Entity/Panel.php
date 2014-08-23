<?php

namespace DebugKit\Model\Entity;

use Cake\ORM\Entity;

/**
 * Panel entity class.
 */
class Panel extends Entity {

/**
 * Some fields should not be in JSON/array exports.
 *
 * @var array
 */
	protected $_hidden = ['content'];

}
