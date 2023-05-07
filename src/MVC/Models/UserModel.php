<?php

namespace fvucemilo\phpmvc\MVC\Models;

use fvucemilo\phpmvc\DB\ORM\ActiveRecord;

/**
 * UserModel is an abstract class that extends the ActiveRecord class and defines an abstract method for getting the display name.
 * The class should be extended by other user models in the application that require the basic functionality provided by the ActiveRecord class.
 * The extending class must implement the getDisplayName method that returns the display name of the user.
 */
abstract class UserModel extends ActiveRecord
{
    /**
     * Returns the display name of the user.
     *
     * @return string The display name of the user.
     */
    abstract public function getDisplayName(): string;
}