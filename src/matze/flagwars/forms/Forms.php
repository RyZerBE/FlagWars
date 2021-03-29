<?php

namespace matze\flagwars\forms;

use matze\flagwars\forms\types\SelectMapForm;
use matze\flagwars\forms\types\SelectTeamForm;

class Forms {

    /** @var  */
    private static $selectTeamForm = null;

    /**
     * @return Form
     */
    public static function getSelectTeamForm(): Form {
        if(is_null(self::$selectTeamForm)) {
            self::$selectTeamForm = new SelectTeamForm();
        }
        return self::$selectTeamForm;
    }

    /** @var  */
    private static $selectMapForm = null;

    /**
     * @return Form
     */
    public static function getSelectMapForm(): Form {
        if(is_null(self::$selectMapForm)) {
            self::$selectMapForm = new SelectMapForm();
        }
        return self::$selectMapForm;
    }
}