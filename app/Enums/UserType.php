<?php

namespace App\Enums;

enum UserType:string
{
    case SUPER_ADMIN='super_admin';

    case SCHOOL_ADMIN='school_admin';

    case PRINCIPAL='principal';

    case TEACHER='teacher';

    case PARENT='parent';

    case STUDENT='student';

    case ACCOUNTANT='accountant';

    case LIBRARIAN='librarian';

    case STAFF='staff';
}