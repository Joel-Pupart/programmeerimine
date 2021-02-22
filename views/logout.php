<?php

unset($_SESSION[IS_LOGGED_IN]);
session_destroy();

redirect('/login');