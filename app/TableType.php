<?php

namespace App;

enum TableType: int
{
    case Clubs = 1;
    case ClubMember = 2;
    case Events = 3;
    case EventMember = 4;
    case Items = 5;
    case ItemMember = 6;
    case Members = 7;
    case MemberRole = 8;
    case MemberSection = 9;
    case MemberSubscription = 10;
    case Roles = 11;
    case Sections = 12;
    case Subscriptions = 13;
    case Users = 14;
}
