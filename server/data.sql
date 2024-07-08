START TRANSACTION;

INSERT INTO `services` (`serviceId`, `name`, `description`) VALUES
(1, 'Restauration', 'Dégustez les plats du coin en admirant le paysage!'),
(2, 'Visite des habitats avec un guide', 'Visitez les habitats du zoo avec un guide, gratuitement!'),
(3, 'Visite du zoo en petit train', 'Faites un tour du zoo en petit train!');

INSERT INTO `habitats` (`habitatId`, `name`, `description`) VALUES
(1, 'Savane', 'Un environnement vaste, herbeux et chaud.'),
(2, 'Jungle', 'Une forêt tropicale dense, verte et luxuriante.'),
(3, 'Marais', 'Une zone humide envahie par la végétation aquatique.');

INSERT INTO `habitatThumbnails` (`habitatThumbId`, `habitat`, `source`) VALUES
(1, 1, '/content/uploads/habitat/79f619d9788a8b03c4a71fc11babbd23a6948836.png'),
(2, 1, '/content/uploads/habitat/57eec92c0d50207aac419011e0bb777faf450359.png'),
(3, 2, '/content/uploads/habitat/aed91a6c554615e1162ab7f103536fa7d89341c5.jpg'),
(4, 2, '/content/uploads/habitat/a0275ac05e969a395d08833b9930163ce0bcd1c7.jpg'),
(5, 3, '/content/uploads/habitat/7c91c7b191362ada407469e817d015b700eb02e4.jpg'),
(6, 3, '/content/uploads/habitat/6dcd0ff5321eaf05167750866f7a62de5e342cc3.jpg');

COMMIT;