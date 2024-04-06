CREATE TABLE `friend` (
  `steamid` bigint(255) NOT NULL,
  `personaname` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `loccountrycode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `history` (
  `id` bigint(255) NOT NULL,
  `me` bigint(255) DEFAULT NULL,
  `them` bigint(255) DEFAULT NULL,
  `type` int(1) DEFAULT '0',
  `since` datetime DEFAULT '0000-00-00 00:00:00',
  `previous_name` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `current_name` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `previous_avatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `current_avatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user` (
  `steamid` bigint(255) NOT NULL,
  `communityvisibilitystate` int(1) NOT NULL,
  `personaname` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `friends` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `friends_count` int(4) NOT NULL,
  `added` int(1) NOT NULL DEFAULT '1',
  `deleted` int(1) NOT NULL DEFAULT '1',
  `renamed` int(1) NOT NULL DEFAULT '1',
  `timezone` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '+00:00',
  `per_page` int(3) NOT NULL DEFAULT '25',
  `theme` int(1) NOT NULL DEFAULT '1',
  `membersince` datetime NOT NULL,
  `lastlogin` datetime NOT NULL,
  `cookie` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `refresh` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `friend`
  ADD PRIMARY KEY (`steamid`);

ALTER TABLE `history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQUE` (`me`,`them`,`type`,`since`,`date`) USING BTREE;

ALTER TABLE `user`
  ADD PRIMARY KEY (`steamid`);
