-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 26, 2015 alle 11:35
-- Versione del server: 5.6.20
-- PHP Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `webapp`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `Aziende`
--

CREATE TABLE IF NOT EXISTS `Aziende` (
`id` int(11) NOT NULL,
  `nome_azienda` text NOT NULL,
  `ragione_sociale` text NOT NULL,
  `citta` text NOT NULL,
  `indirizzo` text NOT NULL,
  `email` text NOT NULL,
  `partita_iva` text NOT NULL,
  `valutazione` float(11,1) DEFAULT NULL,
  `num_valutazioni` int(11) NOT NULL,
  `tipo_attivita` varchar(50) NOT NULL,
  `descrizione` text,
  `modalita_pagamento` varchar(50) NOT NULL,
  `limite_spesa` int(11) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dump dei dati per la tabella `Aziende`
--

INSERT INTO `Aziende` (`id`, `nome_azienda`, `ragione_sociale`, `citta`, `indirizzo`, `email`, `partita_iva`, `valutazione`, `num_valutazioni`, `tipo_attivita`, `descrizione`, `modalita_pagamento`, `limite_spesa`) VALUES
(2, 'Idraulic', 'srl', 'bologna', 'Bologna via zamboni 99', 'ciotto-ciotto@hotmail.it', '66666', NULL, 0, '', NULL, '', 0),
(3, 'Acqua', 'srl', 'Bologna', 'Bologna Via Stalingrado 187', 'info@acquasrl.it', '7654321', 2.5, 1, '', NULL, '', 0),
(11, 'Mia Azienda', 'srl', 'Crevalcore', ' Crevalcore Via Roma 33', 'info@miaazienda.com', '1234567', 4.0, 2, '', NULL, '', 0),
(12, 'Geovest', 'spa', 'Bologna', 'Bologna Via Morandi 57', 'ciotto-ciotto@hotmail.it', '98765', 3.5, 6, '', NULL, '', 0),
(13, 'Test Azienda', 'sas', 'Crevalcore', 'Via Mssimo 44', '9ciotto1@gmail.com', 'test123455', NULL, 0, '', NULL, '', 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `Disponibili`
--

CREATE TABLE IF NOT EXISTS `Disponibili` (
  `id_fornitore` int(11) NOT NULL,
  `disponibile` tinyint(1) NOT NULL,
  `posizione_x` text NOT NULL,
  `posizione_y` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `Disponibili`
--

INSERT INTO `Disponibili` (`id_fornitore`, `disponibile`, `posizione_x`, `posizione_y`) VALUES
(7, 0, '44.7082992', '11.1325263'),
(8, 1, '44.5394036', '11.1369385'),
(12, 1, '44.5019424', '11.3493327'),
(15, 1, '44.5019424', '11.3493327'),
(52, 1, '44.5019424', '11.3493327');

-- --------------------------------------------------------

--
-- Struttura della tabella `Fatture`
--

CREATE TABLE IF NOT EXISTS `Fatture` (
`id` int(11) NOT NULL,
  `id_azienda` int(11) NOT NULL,
  `mese` int(11) NOT NULL,
  `anno` int(11) NOT NULL,
  `pagato` tinyint(1) NOT NULL DEFAULT '0',
  `data_fatturazione` datetime NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dump dei dati per la tabella `Fatture`
--

INSERT INTO `Fatture` (`id`, `id_azienda`, `mese`, `anno`, `pagato`, `data_fatturazione`) VALUES
(1, 11, 1, 2015, 0, '2015-01-15 12:26:34');

-- --------------------------------------------------------

--
-- Struttura della tabella `Feedback`
--

CREATE TABLE IF NOT EXISTS `Feedback` (
`id` int(11) NOT NULL,
  `id_utente` int(11) NOT NULL,
  `id_fornitore` int(11) NOT NULL,
  `id_azienda` int(11) NOT NULL,
  `data` date NOT NULL,
  `valutazione_utente` decimal(11,1) DEFAULT NULL,
  `valutazione_fornitore` decimal(11,1) DEFAULT NULL,
  `esito` text
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=54 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `Impostazioni`
--

CREATE TABLE IF NOT EXISTS `Impostazioni` (
`option_id` int(11) NOT NULL,
  `option_name` varchar(65) NOT NULL,
  `option_value` text,
  `option_last_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dump dei dati per la tabella `Impostazioni`
--

INSERT INTO `Impostazioni` (`option_id`, `option_name`, `option_value`, `option_last_modified`) VALUES
(1, 'auto_update', 'false', '2015-02-02 16:20:53'),
(2, 'tempo_default', '15', '2015-02-23 17:15:47');

-- --------------------------------------------------------

--
-- Struttura della tabella `Richieste`
--

CREATE TABLE IF NOT EXISTS `Richieste` (
`id` int(11) NOT NULL,
  `id_utente` int(11) NOT NULL,
  `id_fornitore` int(11) NOT NULL,
  `esito` text NOT NULL,
  `tipo_richiesta` text NOT NULL,
  `minuti_risposta` int(11) NOT NULL,
  `distanza` float(11,1) NOT NULL,
  `data_ora` datetime NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=93 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `Users`
--

CREATE TABLE IF NOT EXISTS `Users` (
`id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cognome` varchar(50) NOT NULL,
  `codfiscale` varchar(50) NOT NULL,
  `indirizzo` varchar(50) NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `dati_contatto` varchar(50) NOT NULL,
  `minuti_attesa` int(11) DEFAULT NULL,
  `partita_iva` varchar(50) DEFAULT NULL,
  `principale` tinyint(1) DEFAULT '0',
  `id_azienda` int(11) DEFAULT NULL,
  `tipo_attivita` varchar(50) DEFAULT NULL,
  `descrizione` varchar(50) DEFAULT NULL,
  `modalita_pagamento` varchar(50) DEFAULT NULL,
  `limite_spesa` varchar(50) DEFAULT NULL,
  `contatti_mensili` int(11) DEFAULT NULL,
  `rating` float(11,1) DEFAULT NULL,
  `num_valutazioni` int(11) NOT NULL,
  `email_pushbullet` text,
  `data_reg` date DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=128 ;

--
-- Dump dei dati per la tabella `Users`
--

INSERT INTO `Users` (`id`, `nome`, `cognome`, `codfiscale`, `indirizzo`, `telefono`, `email`, `password`, `dati_contatto`, `minuti_attesa`, `partita_iva`, `principale`, `id_azienda`, `tipo_attivita`, `descrizione`, `modalita_pagamento`, `limite_spesa`, `contatti_mensili`, `rating`, `num_valutazioni`, `email_pushbullet`, `data_reg`) VALUES
(1, 'Alessandro', 'Mercurio', 'mrclsn24g76i', 'crevalcore via s.agata', '3383344566', 'alessandro.mercurio91@gmail.com', '1234', '011', NULL, NULL, 0, NULL, '', '', '', '', NULL, 4.0, 2, '9ciotto1@gmail.com', '2014-12-16'),
(2, 'admin', 'admin', 'dmn76g88i', 'bologna', '3386677899', 'admin@webapp.it', 'admin', '111', NULL, NULL, 99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '', '2014-11-16'),
(7, 'Tizio', 'Caio', 'tzcc67j4o9', 'bologna', '3386677899', 'tizio@gmail.com', '1234', '111', 0, '1234567', 0, 11, 'Elettricista', 'riparazioni', 'paypal', '60', 2, 3.2, 0, '', '2014-12-16'),
(8, 'Mario', 'Rossi', 'mrrss25h78jk9i', 'San Giovanni in P. Via Bologna 22', '3381123344', 'mario.rossi@gmail.com', '1234', '111', NULL, '1234567', 1, 11, 'Elettricista', 'riparazioni e installazione nuovi impianti', 'paypal', '150', 0, 4.5, 1, '9ciotto1@gmail.com', '2014-12-16'),
(10, 'Luigi', 'Bianchi', 'lgbnc45hkfs89i', 'Nonantola Via Modena 14', '3334456677', 'luigi.bianchi@gmail.com', '1234', '111', NULL, NULL, 0, NULL, '', '', '', '', NULL, NULL, 0, NULL, '2014-12-16'),
(12, 'Luca', 'Verdi', 'lcvrd67ht82u8i', 'Crevalcore Via Roma 25', '3387765544', 'luca.verdi@gmail.com', '1234', '111', 16, '98765', 1, 12, 'Idraulico', 'riparazioni', 'paypal', '50', 0, 3.0, 1, '9ciotto1@gmail.com', '2014-12-16'),
(15, 'Viola', 'Grigi', 'vlgrg67h8nj90i', 'Bologna Via Zamboni 55', '3387794433', 'viola.grigi@gmail.com', '1234', '010', 0, '7654321', 0, 3, 'Elettricista', 'riparazioni', 'paypal', '80', 0, NULL, 0, '', '2014-12-16'),
(17, 'Bruno', 'Pignatti', 'brnpgn56h24i87y', 'Nonantola Via Modena 46', '3387790022', 'bruno.pignatti@gmail.com', '1234', '111', 0, '7654321', 1, 3, 'Falegname', 'riparazioni', 'paypal', '100', 0, NULL, 0, '', '2014-12-16'),
(52, 'Alessandro', 'Mercurio', 'lssndmrc98hf7ji', 'Crevalcore Via S.Agata 231/A', '3381123344', 'ciotto-ciotto@hotmail.it', '1234', '010', NULL, '7654321', 1, 3, 'Elettricista', 'Riparazioni e installazione nuovi impianti.', 'paypal', '200', 0, NULL, 0, '9ciotto1@gmail.com', '2014-12-21'),
(53, 'administrator', 'administrator', 'admin', 'Bologna', '3334455677', 'administrator@gmail.com', '1LcL880y', '000', NULL, NULL, 99, NULL, '', '', '', '', NULL, NULL, 0, '9ciotto1@gmail.com', '2015-01-14'),
(127, 'Mario', 'Bianchi', 'MRABNC91H24G467I', 'Bologna Via Irnerio 72', '051', '9ciotto1@gmail.com', '5555', '010', 16, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '', '2015-02-23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Aziende`
--
ALTER TABLE `Aziende`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Disponibili`
--
ALTER TABLE `Disponibili`
 ADD UNIQUE KEY `id_fornitore` (`id_fornitore`);

--
-- Indexes for table `Fatture`
--
ALTER TABLE `Fatture`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Feedback`
--
ALTER TABLE `Feedback`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Impostazioni`
--
ALTER TABLE `Impostazioni`
 ADD PRIMARY KEY (`option_id`), ADD UNIQUE KEY `option_name` (`option_name`);

--
-- Indexes for table `Richieste`
--
ALTER TABLE `Richieste`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `codfiscale` (`codfiscale`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Aziende`
--
ALTER TABLE `Aziende`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `Fatture`
--
ALTER TABLE `Fatture`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `Feedback`
--
ALTER TABLE `Feedback`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=54;
--
-- AUTO_INCREMENT for table `Impostazioni`
--
ALTER TABLE `Impostazioni`
MODIFY `option_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `Richieste`
--
ALTER TABLE `Richieste`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=93;
--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=128;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
