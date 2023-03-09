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
        MYSQL_CONFIG_NOT_EXISTS         = "MYSQL config not exists!",
        LISTO_PARAMETER_IS_NOT_ARRAY       = "Parameter is't array!";

    public const
        MYSQL_PARAMETER_IS_NOT_VALID    = ["MYSQL CONFIG ERROR: parameter '%s' in config file is not valid!",20101],
        MYSQL_PARAMETER_NOT_DEFINED     = ["MYSQL CONFIG ERROR: the '%s' paramater in config file is not defined!",20102],
        GG ="";

}