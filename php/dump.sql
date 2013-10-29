SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE IF NOT EXISTS `articoliconsigliati` (
  `articolomenu1` varchar(50) NOT NULL,
  `articolomenu2` varchar(50) NOT NULL,
  PRIMARY KEY (`articolomenu1`,`articolomenu2`),
  KEY `articolomenu2` (`articolomenu2`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Definisce gli articoli consigliati per ogni articolo';


CREATE TABLE IF NOT EXISTS `articolimenu` (
  `articolomenu` varchar(50) NOT NULL,
  `categoriamenu` varchar(50) NOT NULL,
  `descrizione` text,
  `foto` varchar(50) DEFAULT NULL,
  `disponibile` tinyint(1) NOT NULL DEFAULT '1',
  `prezzo` decimal(10,0) NOT NULL,
  PRIMARY KEY (`articolomenu`),
  KEY `categoriamenu` (`categoriamenu`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tutti gli articoli del menu (es. pizza margherita, guinness ';

INSERT INTO `articolimenu` (`articolomenu`, `categoriamenu`, `descrizione`, `foto`, `disponibile`, `prezzo`) VALUES
('Dolce1', 'dolci', 'Questo Ã¨ il dolce numero 1', NULL, 1, 99999),
('Dolce2', 'dolci', NULL, NULL, 1, 132894),
('Dolce3', 'dolci', NULL, NULL, 1, 55),
('Primo 2', 'primi', NULL, NULL, 1, 5),
('Primo1', 'primi', 'saÃ²dlkjfaÃ²lcxalk akjzx\r\nc\r\nxzcvlozxcjvÃ²lkzxc\r\nas\r\nd\r\naewaefas', NULL, 1, 10);

CREATE TABLE IF NOT EXISTS `articolimenu_ingredienti` (
  `articolomenu` varchar(50) NOT NULL,
  `ingrediente` varchar(50) NOT NULL,
  PRIMARY KEY (`articolomenu`,`ingrediente`),
  KEY `ingrediente` (`ingrediente`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='collega gli articoli del menu con gli ingredienti di cui son';


CREATE TABLE IF NOT EXISTS `articoliprenotati` (
  `num_articolo` int(11) NOT NULL AUTO_INCREMENT,
  `num_prenotazione` int(11) NOT NULL,
  `articolomenu` varchar(50) NOT NULL,
  `quantita` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`num_articolo`),
  KEY `num_prenotazione` (`num_prenotazione`),
  KEY `articolomenu` (`articolomenu`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `categorieconsigliate` (
  `categoriamenu1` varchar(50) NOT NULL,
  `categoriamenu2` varchar(50) NOT NULL,
  PRIMARY KEY (`categoriamenu1`,`categoriamenu2`),
  KEY `categoriamenu2` (`categoriamenu2`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `categoriecontenuti` (
  `categoriacontenuto` varchar(50) NOT NULL,
  `tipocontenuto` varchar(50) NOT NULL,
  PRIMARY KEY (`categoriacontenuto`),
  KEY `tipocontenuto` (`tipocontenuto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Le categorie dei contenuti (es. concerto folk, partita juve)';

INSERT INTO `categoriecontenuti` (`categoriacontenuto`, `tipocontenuto`) VALUES
('concerto folk', 'evento'),
('concerto metal', 'evento'),
('partita', 'evento'),
('info', 'info');

CREATE TABLE IF NOT EXISTS `categoriemenu` (
  `categoriamenu` varchar(50) NOT NULL,
  `peso` int(11) NOT NULL DEFAULT '25',
  `descrizione` text,
  PRIMARY KEY (`categoriamenu`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Le categorie del menÃ¹ (es. primi piatti, birre, ecc..)';

INSERT INTO `categoriemenu` (`categoriamenu`, `peso`, `descrizione`) VALUES
('contorni', 30, 'I nostri contorni sono ottimi da prendere prima della frutta, durante il secondo e dopo il primo!! Ma va???'),
('dolci', 35, 'I nostri dolci vanno bene in qualunque momento, in qualunque stato d''animo e in qualunque situazione! PerchÃ¨ dire di no??'),
('primi', 10, 'Questo Ã¨ un primo... Lo conosci? Io sÃ¬.'),
('secondi', 15, NULL);

CREATE TABLE IF NOT EXISTS `categoriemenu_ingredienti` (
  `categoriamenu` varchar(50) NOT NULL,
  `ingrediente` varchar(50) NOT NULL,
  PRIMARY KEY (`categoriamenu`,`ingrediente`),
  KEY `ingrediente` (`ingrediente`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Collega le categorie di menÃ¹ con le variazioni possibili pe';


CREATE TABLE IF NOT EXISTS `categorieutenti` (
  `categoriautente` varchar(50) NOT NULL,
  `priorita` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`categoriautente`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Le categorie di utenti';

INSERT INTO `categorieutenti` (`categoriautente`, `priorita`) VALUES
('admin', 99),
('collaboratore', 70),
('guest', 0),
('moderatore', 50),
('redattore', 20),
('superuser', 100);

CREATE TABLE IF NOT EXISTS `categorieutenti_privilegi` (
  `categoriautente` varchar(50) NOT NULL,
  `privilegio` varchar(50) NOT NULL,
  PRIMARY KEY (`categoriautente`,`privilegio`),
  KEY `privilegio` (`privilegio`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Collega le categorie utenti con i privilegi associati';

INSERT INTO `categorieutenti_privilegi` (`categoriautente`, `privilegio`) VALUES
('admin', 'articoli consigliati'),
('superuser', 'articoli consigliati'),
('admin', 'articoli menu'),
('superuser', 'articoli menu'),
('superuser', 'categorie consigliate'),
('admin', 'categorie eventi'),
('collaboratore', 'categorie eventi'),
('superuser', 'categorie eventi'),
('admin', 'categorie menu'),
('superuser', 'categorie menu'),
('superuser', 'categorie utenti'),
('collaboratore', 'eliminare utenti'),
('superuser', 'eliminare utenti'),
('admin', 'eventi'),
('collaboratore', 'eventi'),
('redattore', 'eventi'),
('superuser', 'eventi'),
('admin', 'info'),
('collaboratore', 'info'),
('moderatore', 'info'),
('redattore', 'info'),
('superuser', 'info'),
('admin', 'ingredienti'),
('superuser', 'ingredienti'),
('admin', 'orari'),
('superuser', 'orari'),
('admin', 'privilegiare utenti'),
('collaboratore', 'privilegiare utenti'),
('moderatore', 'privilegiare utenti'),
('superuser', 'privilegiare utenti'),
('admin', 'profilo'),
('collaboratore', 'profilo'),
('guest', 'profilo'),
('moderatore', 'profilo'),
('redattore', 'profilo'),
('superuser', 'profilo'),
('admin', 'tipi contenuti'),
('superuser', 'tipi contenuti'),
('admin', 'variazioni possibili'),
('superuser', 'variazioni possibili'),
('collaboratore', 'visualizzare utenti'),
('moderatore', 'visualizzare utenti'),
('superuser', 'visualizzare utenti');

CREATE TABLE IF NOT EXISTS `contenuti` (
  `num_contenuto` int(11) NOT NULL AUTO_INCREMENT,
  `utente` varchar(50) NOT NULL,
  `categoriacontenuto` varchar(50) NOT NULL,
  `data_creazione` int(11) NOT NULL,
  `titolo` varchar(50) NOT NULL,
  `testo` text,
  `foto` varchar(50) DEFAULT NULL,
  `data_2` int(11) DEFAULT NULL,
  `peso` int(11) NOT NULL DEFAULT '25',
  `in_cima` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`num_contenuto`),
  KEY `utente` (`utente`),
  KEY `categoriacontenuto` (`categoriacontenuto`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Contiene tutti i contenuti del sito' AUTO_INCREMENT=48 ;

INSERT INTO `contenuti` (`num_contenuto`, `utente`, `categoriacontenuto`, `data_creazione`, `titolo`, `testo`, `foto`, `data_2`, `peso`, `in_cima`) VALUES
(7, 'Chosko', 'info', 1327066197, 'Orari', 'Il ristorante ha i seguenti orari:<br><br>Marted&igrave; - Venerd&igrave;:<br>12:00 - 15:00<br>19:00 - 23:30<br><br>Sabato:<br>12:00 - 15:00<br>19:00 - 02:00<br><br>Domenica:<br>12:00 - 16:00<br>19:00 - 23:30<br><br>Luned&igrave; chiuso', NULL, 1344808800, 25, 1),
(38, 'Admin1', 'concerto folk', 1327054572, 'Selezioni Miss Italia 2011', 'Il nostro locale &egrave; lieto di ospitare le preselezioni piemontesi di Miss Italia 2012. Dalle 21.00 in poi potrete ammirare tutte queste bellissime ragazze che si preparano all''avventura di Miss Italia. I posti sono limitati, &egrave; gradita la prenotazione.', NULL, 1326225900, 50, 0),
(39, 'Admin1', 'concerto folk', 1327066056, 'Aspettando il weekend', 'Ormai il weekend &egrave; alle porte, e gi&agrave; sapete cosa vi aspetta...<br>Vi &egrave; sfuggito? Date un occhiata agli eventi futuri...<br><br>Tanto per prepararsi il gargarozzo, solo per questa sera, ogni tre birre una in omaggio...', NULL, 1327698300, 14, 0),
(42, 'Admin1', 'concerto folk', 1327066045, 'Saturday DEVASTATION', 'Avete problemi di glicemia?<br><br>Avete problemi di pressione alta?<br><br>Allora &eacute; meglio che questa serata la passate a casa...<br>Solo per questa sera, ogni birra media, di qualunque casa,avrete in omaggio un hotdog!<br><br>Non vi piacciono gli hotdog? Nessun problema, infatti potrete anche scegliere tra i nostri panini e le nostre piadine.<br><br>Una occasione che non potete farvi scappare.', NULL, 1327784400, 1, 0),
(43, 'Admin2', 'partita', 1327065982, 'Proiezione della partita Catania-Parma', 'Verr&agrave; proiettata la partita Catania-Parma, alla fine della quale ci sar&agrave; un rinfresco a buffet. <br><br>La proiezione della partita sara gratuita e a ingresso libero, mentre per il buffet ci sara un costo fisso di 5 euro.', NULL, 1327856400, 20, 0),
(44, 'Admin2', 'concerto folk', 1327066107, 'Concerto dei Modena City Ramblers', 'Oggi suoneranno per noi i Modena City Ramblers, il famoso gruppo suoner&agrave; fino alle 23:00 per allietare la vostra serata con la loro musica folk.', NULL, 1330547400, 10, 1),
(45, 'Admin2', 'concerto folk', 1327061550, 'Sconti &quot;Giro Pizza&quot;', 'Solo per questa domenica si potr&agrave; ordinare il &quot;giro pizza&quot; a 10 euro al posto di 14! Si ricorda minimo 4 persone.', NULL, 1327341600, 10, 0),
(46, 'Admin2', 'concerto folk', 1327066078, 'Serata caraoke', 'cosa c''&egrave; di meglio per salutare il week end di una serata caraoke nel vostro ristorante preferito?<br><br>Dalle 18:30 alle 0:00 del giorno dopo si potranno cantare a squarciagola le migliori canzoni italiane di sempre.', NULL, 1327944600, 32, 0),
(47, 'Admin1', 'info', 1327066254, 'Apertura straordinaria', 'Si avvisa la gentile clientela che MERCOLEDI'' 25 GENNAIO il locale rimarr&agrave; aperto fino alle ore 02:00 per festeggiare il compleanno del titolare.', NULL, 1327460400, 25, 0);

CREATE TABLE IF NOT EXISTS `ingredienti` (
  `ingrediente` varchar(50) NOT NULL,
  `disponibile` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ingrediente`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tutti gli ingredienti (es. mozzarella di bufala, origano) ec';


CREATE TABLE IF NOT EXISTS `prenotazioni` (
  `num_prenotazione` int(11) NOT NULL AUTO_INCREMENT,
  `utente` varchar(50) NOT NULL,
  `num_tavolo` int(11) DEFAULT NULL,
  `statoprenotazione` varchar(50) NOT NULL,
  `temporeale` tinyint(1) NOT NULL DEFAULT '0',
  `dataprenotazione` timestamp NULL DEFAULT NULL,
  `coperti` int(11) NOT NULL,
  PRIMARY KEY (`num_prenotazione`),
  KEY `utente` (`utente`),
  KEY `num_tavolo` (`num_tavolo`),
  KEY `statoprenotazione` (`statoprenotazione`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `privilegi` (
  `privilegio` varchar(50) NOT NULL,
  `descrizione` varchar(100) NOT NULL,
  PRIMARY KEY (`privilegio`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `privilegi` (`privilegio`, `descrizione`) VALUES
('articoli consigliati', 'gestire gli articoli consigliati'),
('articoli menu', 'gestire le voci del men&ugrave;'),
('categorie consigliate', 'selezionare quali categorie del men&ugrave; possono essere consigliate ad altre categorie'),
('categorie eventi', 'gestire le categorie di eventi'),
('categorie menu', 'gestire le categorie del menu'),
('categorie utenti', 'creare nuove categorie di utenti, modificarle o eliminarle'),
('eliminare utenti', 'eliminare gli utenti'),
('eventi', 'gestire gli eventi'),
('info', 'gestire le info'),
('ingredienti', 'gestire gli ingredienti del menu'),
('orari', 'gestire gli orari dell''attivit&agrave;'),
('privilegiare utenti', 'privilegiare o declassare utenti'),
('profilo', 'accedere all''area privata'),
('tipi contenuti', 'gestire i tipi dei contenuti'),
('variazioni possibili', 'gestire le variazioni possibili per ogni categoria del men&ugrave;'),
('visualizzare utenti', 'visualizzare gli utenti del sito');

CREATE TABLE IF NOT EXISTS `statiprenotazioni` (
  `statoprenotazione` varchar(50) NOT NULL,
  PRIMARY KEY (`statoprenotazione`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `tavoli` (
  `num_tavolo` int(11) NOT NULL,
  `utente` varchar(50) NOT NULL,
  PRIMARY KEY (`num_tavolo`),
  UNIQUE KEY `utente` (`utente`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `tipicontenuti` (
  `tipocontenuto` varchar(50) NOT NULL,
  PRIMARY KEY (`tipocontenuto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Il tipo di evento (es. eventi oppure info)';

INSERT INTO `tipicontenuti` (`tipocontenuto`) VALUES
('evento'),
('info');

CREATE TABLE IF NOT EXISTS `utenti` (
  `utente` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `nome` varchar(50) DEFAULT NULL,
  `cognome` varchar(50) DEFAULT NULL,
  `dataregistrazione` int(11) NOT NULL,
  `telefono` varchar(14) NOT NULL,
  `telefono2` varchar(14) DEFAULT NULL,
  `indirizzo` varchar(50) DEFAULT NULL,
  `datanascita` int(11) NOT NULL,
  `categoriautente` varchar(50) NOT NULL DEFAULT 'guest',
  `attivo` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`utente`),
  UNIQUE KEY `email` (`email`),
  KEY `categoriautente` (`categoriautente`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tutti gli utenti registrati al sito';

INSERT INTO `utenti` (`utente`, `email`, `password`, `nome`, `cognome`, `dataregistrazione`, `telefono`, `telefono2`, `indirizzo`, `datanascita`, `categoriautente`, `attivo`) VALUES
('Abde3', 'abdelfatiha@hotmail.it', '7fc4fee210898c61d86810b348af6472', NULL, NULL, 1327058770, '3016541', NULL, NULL, 726447600, 'guest', 1),
('Admin1', 'admin1@theroyalprincess.com', 'e00cf25ad42683b3df678c61f42c6bda', NULL, NULL, 1327006015, '5555555555', NULL, NULL, -63162000, 'admin', 1),
('Admin2', 'admin2@theroyalprincess.com', 'c84258e9c39059a89ab77d846ddab909', NULL, NULL, 1327006071, '5555555555', NULL, NULL, 915145200, 'admin', 1),
('Admin3', 'admin3@theroyalprincess.com', '32cacb2f994f6b42183a1300d9a3e8d6', NULL, NULL, 1327006161, '5555555555', NULL, NULL, 552607200, 'admin', 0),
('Alex', 'alessandroricciardi@hotmail.com', 'a08372b70196c21a9229cf04db6b7ceb', 'Alessandro', 'Ricciardi', 1327057975, '3257851495', '0115987463', 'Strada del Portone 33', -488250000, 'guest', 1),
('Chosko', 'choskoxteam@hotmail.it', 'cf3221dde4df7b9054719437156661f4', 'Ruben', 'Caliandro', 2012, '3337368333', NULL, 'Via P.Giuria 22', 644709600, 'superuser', 1),
('Collaboratore1', 'riky@libero.it', '3a2b511837284833731d3bdbc682318e', 'Riccardo', 'Malafemmina', 1327061991, '3587412036', NULL, NULL, 619826400, 'collaboratore', 1),
('Collaboratore2', 'rapacchio@gmail.com', 'bae1cd2e79bc9da2605c914106657a62', 'Ottavio', NULL, 1327062129, '3458796024', NULL, 'Via Pallanzio 56', -90000, 'collaboratore', 1),
('Collaboratore3', 'bellfiore@fastwebnet.com', 'e7d86f80134dc6134d02674d29fdace7', 'Fioravante', 'Bellini', 1327062316, '3458792005', NULL, NULL, -617936400, 'collaboratore', 0),
('Fabry86', 'fabryrec@yahoo.it', 'dba6b3b30b87d8cffa2b3685a9c23ebe', NULL, NULL, 1327058092, '3145873259', NULL, NULL, 511398000, 'guest', 1),
('Gerry', 'gerry60b@fastwebnet.com', 'b7b7c4c378e9875e7c42877c800006c5', 'Gerardo', NULL, 1327059967, '30469874022', NULL, 'Via Castello 10', -287542800, 'guest', 1),
('lello', 'lelo@gmail.com', '741251a82e793eb9181ceedff8240bb8', NULL, NULL, 1327060442, '3418795004', NULL, NULL, 443487600, 'guest', 1),
('Mimma', 'mimmarini@gmail.com', '1be9a312bc3d18ebdd092076ae197d37', 'Emanuela', 'Barini', 1327058193, '3958745201', NULL, 'Corso Galimberti 4', 660956400, 'guest', 1),
('Moderatore1', 'moderatore1@theroyalprincess.com', 'e9cd66ad4a7334acc4679eb2a42c971f', NULL, NULL, 1327006325, '5555555555', NULL, NULL, 697503600, 'moderatore', 1),
('Moderatore2', 'moderatore2@theroyalprincess.com', '7221babea36d8d5a183784804c188668', NULL, NULL, 1327006401, '5555555555', NULL, NULL, 245718000, 'moderatore', 1),
('Moderatore3', 'moderatore3@theroyalpricess.it', '6db4c4bd85c3e03f8a43db377b048e33', NULL, NULL, 1327006389, '3336987452', NULL, NULL, 915145200, 'moderatore', 0),
('Pippo', 'filippogermine@hotmail.it', '4a057a33f1d8158556eade51342786c6', NULL, 'Germine', 1327058350, '3105874659', '34587901', NULL, -244170000, 'guest', 1),
('Ramona', 'ramonac@tiscali.com', '071e89d6430e8321624257dd60cf80e6', 'Ramona', 'Catalano', 1327058596, '3257894100', NULL, 'Via dei Mille 20, Pescara', 915145200, 'guest', 0),
('Redattore1', 'hask@libero.it', '83edb3e0071385cd35465bf7b39f4c65', 'Hashis', 'Kebab', 1327060334, '3854169789', NULL, 'Corso Sebastopoli 450', -439779600, 'redattore', 1),
('Redattore2', 'germrap@libero.it', '09899dc80e3b76f598ec00b56426f0b6', 'Germina', 'Rapazzo', 1327060628, '378951265', NULL, NULL, -1744938000, 'redattore', 1),
('Redattore3', 'peppone@yahoo.it', '76105714660654f01542fc4624349b50', NULL, NULL, 1327060885, '3017895412', NULL, NULL, 367020000, 'redattore', 0),
('Vivi91', 'vivike@libero.it', 'b44d882b584ea7836b74d4d8e384718a', 'Viorica', 'Zsestokova', 1327059617, '3856941266', NULL, NULL, 672357600, 'guest', 0);

CREATE TABLE IF NOT EXISTS `utenti_categoriecontenuti` (
  `utente` varchar(50) NOT NULL,
  `categoriacontenuto` varchar(50) NOT NULL,
  `avvisami` tinyint(1) NOT NULL,
  PRIMARY KEY (`utente`,`categoriacontenuto`),
  KEY `categoriacontenuto` (`categoriacontenuto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='collega gli utenti con le categorie che preferiscono';


CREATE TABLE IF NOT EXISTS `variazioni` (
  `num_articolo` int(11) NOT NULL,
  `ingrediente` varchar(50) NOT NULL,
  PRIMARY KEY (`num_articolo`,`ingrediente`),
  KEY `ingrediente` (`ingrediente`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



ALTER TABLE `articoliconsigliati`
  ADD CONSTRAINT `articoliconsigliati_ibfk_1` FOREIGN KEY (`articolomenu1`) REFERENCES `articolimenu` (`articolomenu`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `articoliconsigliati_ibfk_2` FOREIGN KEY (`articolomenu2`) REFERENCES `articolimenu` (`articolomenu`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `articolimenu`
  ADD CONSTRAINT `articolimenu_ibfk_1` FOREIGN KEY (`categoriamenu`) REFERENCES `categoriemenu` (`categoriamenu`) ON UPDATE CASCADE;

ALTER TABLE `articolimenu_ingredienti`
  ADD CONSTRAINT `articolimenu_ingredienti_ibfk_1` FOREIGN KEY (`articolomenu`) REFERENCES `articolimenu` (`articolomenu`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `articolimenu_ingredienti_ibfk_2` FOREIGN KEY (`ingrediente`) REFERENCES `ingredienti` (`ingrediente`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `articoliprenotati`
  ADD CONSTRAINT `articoliprenotati_ibfk_1` FOREIGN KEY (`num_prenotazione`) REFERENCES `prenotazioni` (`num_prenotazione`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `articoliprenotati_ibfk_2` FOREIGN KEY (`articolomenu`) REFERENCES `articolimenu` (`articolomenu`) ON UPDATE CASCADE;

ALTER TABLE `categorieconsigliate`
  ADD CONSTRAINT `categorieconsigliate_ibfk_1` FOREIGN KEY (`categoriamenu1`) REFERENCES `categoriemenu` (`categoriamenu`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `categorieconsigliate_ibfk_2` FOREIGN KEY (`categoriamenu2`) REFERENCES `categoriemenu` (`categoriamenu`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `categoriecontenuti`
  ADD CONSTRAINT `categoriecontenuti_ibfk_1` FOREIGN KEY (`tipocontenuto`) REFERENCES `tipicontenuti` (`tipocontenuto`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `categoriemenu_ingredienti`
  ADD CONSTRAINT `categoriemenu_ingredienti_ibfk_1` FOREIGN KEY (`categoriamenu`) REFERENCES `categoriemenu` (`categoriamenu`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `categoriemenu_ingredienti_ibfk_2` FOREIGN KEY (`ingrediente`) REFERENCES `ingredienti` (`ingrediente`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `categorieutenti_privilegi`
  ADD CONSTRAINT `categorieutenti_privilegi_ibfk_1` FOREIGN KEY (`categoriautente`) REFERENCES `categorieutenti` (`categoriautente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `categorieutenti_privilegi_ibfk_2` FOREIGN KEY (`privilegio`) REFERENCES `privilegi` (`privilegio`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `contenuti`
  ADD CONSTRAINT `contenuti_ibfk_4` FOREIGN KEY (`categoriacontenuto`) REFERENCES `categoriecontenuti` (`categoriacontenuto`) ON UPDATE CASCADE;

ALTER TABLE `prenotazioni`
  ADD CONSTRAINT `prenotazioni_ibfk_4` FOREIGN KEY (`utente`) REFERENCES `utenti` (`utente`) ON UPDATE CASCADE,
  ADD CONSTRAINT `prenotazioni_ibfk_5` FOREIGN KEY (`num_tavolo`) REFERENCES `tavoli` (`num_tavolo`) ON UPDATE CASCADE,
  ADD CONSTRAINT `prenotazioni_ibfk_6` FOREIGN KEY (`statoprenotazione`) REFERENCES `statiprenotazioni` (`statoprenotazione`) ON UPDATE CASCADE;

ALTER TABLE `tavoli`
  ADD CONSTRAINT `tavoli_ibfk_1` FOREIGN KEY (`utente`) REFERENCES `utenti` (`utente`);

ALTER TABLE `utenti`
  ADD CONSTRAINT `utenti_ibfk_1` FOREIGN KEY (`categoriautente`) REFERENCES `categorieutenti` (`categoriautente`) ON UPDATE CASCADE;

ALTER TABLE `utenti_categoriecontenuti`
  ADD CONSTRAINT `utenti_categoriecontenuti_ibfk_1` FOREIGN KEY (`utente`) REFERENCES `utenti` (`utente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `utenti_categoriecontenuti_ibfk_2` FOREIGN KEY (`categoriacontenuto`) REFERENCES `categoriecontenuti` (`categoriacontenuto`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `variazioni`
  ADD CONSTRAINT `variazioni_ibfk_1` FOREIGN KEY (`num_articolo`) REFERENCES `articoliprenotati` (`num_articolo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `variazioni_ibfk_2` FOREIGN KEY (`ingrediente`) REFERENCES `ingredienti` (`ingrediente`) ON DELETE CASCADE ON UPDATE CASCADE;
