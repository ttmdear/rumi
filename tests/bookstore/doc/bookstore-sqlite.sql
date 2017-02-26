DROP TABLE IF EXISTS `books`;
CREATE TABLE IF NOT EXISTS `books` (
  `idBook` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` TEXT NOT NULL,
  `idCategory` INTEGER,
  `releaseDate` DATE,
  `releaseDatetime` DATETIME,
  `price` FLOAT NOT NULL
);

DELETE FROM `books`;
INSERT INTO `books` (`idBook`, `name`, `idCategory`, `releaseDate`, `releaseDatetime`, `price`) VALUES
	(1, 'name - 0', NULL, '2000-02-15', '1988-08-18 02:46:14', 95.76),
	(2, 'name - 1', 2, '1919-08-11', '1992-10-17 13:50:08', 57.07),
	(3, 'name - 2', 4, '1936-10-24', '1968-01-06 17:37:17', 99.19),
	(4, 'name - 3', 11, '1909-09-05', '1920-12-21 21:01:46', 86.57),
	(5, 'name - 4', NULL, '1913-01-09', '1990-11-16 18:35:03', 30.55),
	(6, 'name - 5', NULL, '1936-04-07', '1928-04-04 23:52:09', 14.32),
	(7, 'name - 6', 8, '1907-02-01', '2004-12-14 07:40:56', 17.96),
	(8, 'name - 7', 2, '1947-05-09', '1935-05-10 17:13:24', 43.43),
	(9, 'name - 8', 10, '1949-09-12', '1902-09-10 23:27:50', 39.56),
	(10, 'name - 9', 8, '1945-05-20', '1918-05-10 12:18:11', 27.04);

DROP TABLE IF EXISTS `books Categories Dic`;
CREATE TABLE IF NOT EXISTS `books Categories Dic` (
  `idCategory` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `name` TEXT NOT NULL
);

DELETE FROM `books Categories Dic`;
INSERT INTO `books Categories Dic` (`idCategory`, `name`) VALUES
	(1, 'IT'),
	(2, 'Science fiction'),
	(3, 'Satire'),
	(4, 'Drama'),
	(5, 'Action and Adventure'),
	(6, 'Romance'),
	(7, 'Mystery'),
	(8, 'Horror'),
	(9, 'Self help'),
	(10, 'Religion, Spirituality & New Age'),
	(11, 'Journals');

DROP TABLE IF EXISTS `booksTags`;
CREATE TABLE IF NOT EXISTS `booksTags` (
  `idBook` INTEGER NOT NULL,
  `idTag` INTEGER NOT NULL,
  `dateOfAdded` DATETIME NOT NULL,
  CONSTRAINT booksTags_pk PRIMARY KEY (`idBook`, `idTag`)
);

DELETE FROM `booksTags`;

DROP TABLE IF EXISTS `booksTagsDic`;
CREATE TABLE IF NOT EXISTS `booksTagsDic` (
  `idTag` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `tag` TEXT NOT NULL
);

DELETE FROM `booksTagsDic`;
--
-- DROP PROCEDURE IF EXISTS createBooks;
--
-- -- delimiter //
-- -- CREATE PROCEDURE createBooks(IN num INT)
-- --     label1: LOOP
-- --         SET num = num - 1;
-- --
-- --         IF num >= 0 THEN
-- --             INSERT INTO `books`
-- --                 (`idBook`, `name`, `idCategory`, `releaseDate`, `releaseDatetime`, `price`) VALUES
-- --                 (null, CONCAT('name', num), NULL, '2000-02-15', '1988-08-18 02:46:14', 95.76)
-- --             ;
-- --             ITERATE label1;
-- --         END IF;
-- --
-- --         LEAVE label1;
-- --
-- --     END LOOP label1;
-- -- BEGIN
-- -- END;
-- -- delimiter ;
