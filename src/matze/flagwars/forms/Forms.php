<?php

namespace matze\flagwars\forms;

use matze\flagwars\forms\types\SelectKitForm;
use matze\flagwars\forms\types\SelectMapForm;
use matze\flagwars\forms\types\SelectTeamForm;

class Forms {
    private static ?Form $selectTeamForm = null;

    /**
     * @return Form
     */
    public static function getSelectTeamForm(): Form {
        if(is_null(self::$selectTeamForm)) {
            self::$selectTeamForm = new SelectTeamForm();
        }
        return self::$selectTeamForm;
    }

    private static ?Form $selectMapForm = null;

    /**
     * @return Form
     */
    public static function getSelectMapForm(): Form {
        if(is_null(self::$selectMapForm)) {
            self::$selectMapForm = new SelectMapForm();
        }
        return self::$selectMapForm;
    }

    private static ?Form $selectKitForm = null;

    /**
     * @return Form
     */
    public static function getSelectKitForm(): Form {
        if(is_null(self::$selectKitForm)) {
            self::$selectKitForm = new SelectKitForm();
        }
        return self::$selectKitForm;
    }
}