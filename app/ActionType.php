<?php

namespace App;

enum ActionType: int
{
    case Login = 1;
    case Create = 2;
    case Update = 3;
}
