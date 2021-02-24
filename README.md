# Programming homework


Link: https://ta19projekt.ta19pupart.itmajakas.ee/

To get this project working on Your server, You have to add a config.php file, with Your own parameters:  
```php
<?php

return [
    'username' => 'database_username',
    'password' => 'database_password',
    'host' => 'host_name',
    'database' => 'database_name'
];
```
Your database must include tables : users, posts, translations. All of those tables have been described in setup.php file, where variables $createUsers, $createPosts and $createTranslations, include correct CREATE TABLE querys for each of them.
