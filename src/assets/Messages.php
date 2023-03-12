<?php

namespace Pachel\EasyFrameWork;

class Messages
{
    public const
        BASE_CONFIG_NOT_VALID           = "Config is not valid!",
        BASE_APP_NOT_CONFIGURED         = "The app is't configured!",
        BASE_FOLDER_NOT_EXISTS          = "The folder not exists!",
        BASE_CONFIG_MISSING_REQ         = "Missing requirement from config",

        DRAW_TEMPLATE_NOT_FOUND         = "Template not found!",

        ROUTING_PARAMETER_MISSING       = "Parameter missing",
        PARAMETER_TYPE_ERROR            = "Parameter type error!",
        MYSQL_CONFIG_NOT_EXISTS         = "MYSQL config not exists!",
        LISTO_PARAMETER_IS_NOT_ARRAY       = "Parameter is't array!";

    public const
        MYSQL_PARAMETER_IS_NOT_VALID    = ["MYSQL CONFIG ERROR: parameter '%s' in config file is not valid!",20101],
        MYSQL_PARAMETER_NOT_DEFINED     = ["MYSQL CONFIG ERROR: the '%s' paramater in config file is not defined!",20102],
        MYSQL_MODELL_NAME_INVALID       = ["MYSQL MODEL ERROR: the '%s' classname is invalid. Use this: tablename_Modell",20103],
        MYSQL_OBJECT_UPDATE_NOT_ALLOWED = ["MYSQL ERROR: Object update without primary is not allowed!",20104],
        MYSQL_OBJECT_DELETE_NOT_ALLOWED = ["MYSQL ERROR: Object delete in safe mode, without primary is not allowed!",20105],
        MYSQL_NOT_ALLOWED_WITHOUT_WHERE = ["MYSQL ERROR: Törölni nem lehet, csak ha van feltétel",20106],
        MYSQL_SET_IS_EMPTY              = ["MYSQL ERROR: Nincs megadva  a set",20107],
        MYSQL_WHERE_IS_EMPTY              = ["MYSQL ERROR: Nincs megadva  a WHERE",20107],


        MODEL_PROPERY_NOT_EXISTS        = ["MODEL ERROR: '%s' is not defined property in '%s' class!",20200],
        GG ="";

}