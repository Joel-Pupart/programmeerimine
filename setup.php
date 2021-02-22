<?php

$createUsers = "
CREATE TABLE `users`(
    `id` SERIAL,
    `email` VARCHAR(100) NOT NULL,
    `password` VARCHAR(60) NOT NULL,
    `added` DATETIME NOT NULL,
    `added_by` INT NOT NULL,
    `edited` DATETIME ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `edited_by` INT NOT NULL
) ENGINE = InnoDB;
";

$createPosts = "
CREATE TABLE `posts`(
    `id` SERIAL,
    `title` VARCHAR(100) NOT NULL,
    `body` text NOT NULL,
    `status` enum('draft','pubic') DEFAULT 'draft',
    `added` DATETIME NOT NULL,
    `added_by` INT NOT NULL,
    `edited` DATETIME ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `edited_by` INT NOT NULL
) ENGINE = InnoDB;
";

$createTranslations = "
CREATE TABLE `translations`(
    `id` SERIAL,
    `translation_name` VARCHAR(255) NOT NULL,
    `translation` TEXT NOT NULL,
    `language` VARCHAR(3) NOT NULL,
    `model` VARCHAR(50) NOT NULL,
    `model_id` INT NOT NULL
) ENGINE = INNODB;
";