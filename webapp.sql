-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 27, 2015 alle 15:08
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Dump dei dati per la tabella `Aziende`
--

INSERT INTO `Aziende` (`id`, `nome_azienda`, `ragione_sociale`, `citta`, `indirizzo`, `email`, `partita_iva`, `valutazione`, `num_valutazioni`, `tipo_attivita`, `descrizione`, `modalita_pagamento`, `limite_spesa`) VALUES
(14, 'ElectroHome', 'S.R.L.', 'Crevalcore', 'Via Roma 33', '9ciotto1@gmail.com', '12345678903', 4.5, 1, 'elettricista', 'Riparazione e installazione di nuovi impianti elettrici domestici.', 'bonifico', 200);

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
(128, 1, '44.7161986', '11.145876899999962'),
(130, 1, '44.5019424', '11.3493327');

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=59 ;

--
-- Dump dei dati per la tabella `Feedback`
--

INSERT INTO `Feedback` (`id`, `id_utente`, `id_fornitore`, `id_azienda`, `data`, `valutazione_utente`, `valutazione_fornitore`, `esito`) VALUES
(54, 1, 130, 14, '2015-02-26', NULL, NULL, 'scaduta'),
(55, 1, 128, 14, '2015-02-26', NULL, '4.5', 'scaduta'),
(56, 1, 130, 14, '2015-02-27', NULL, NULL, 'accettata'),
(57, 1, 128, 14, '2015-02-27', '5.0', NULL, 'accettata'),
(58, 1, 130, 14, '2015-02-27', NULL, NULL, 'rifiutata');

-- --------------------------------------------------------

--
-- Struttura della tabella `Impostazioni`
--

CREATE TABLE IF NOT EXISTS `Impostazioni` (
`option_id` int(11) NOT NULL,
  `option_name` varchar(65) NOT NULL,
  `option_value` text,
  `option_last_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dump dei dati per la tabella `Impostazioni`
--

INSERT INTO `Impostazioni` (`option_id`, `option_name`, `option_value`, `option_last_modified`) VALUES
(1, 'auto_update', 'false', '2015-02-02 16:20:53'),
(2, 'tempo_default', '15', '2015-02-23 17:15:47'),
(3, 'costo_richiesta', '5', '2015-02-26 19:34:11'),
(4, 'tempo_max', '60', '2015-02-26 17:38:15'),
(5, 'paypal', 'webappartigiani.info@gmail.com', '2015-02-27 11:25:27'),
(6, 'iban', 'IT02 L123 4512 3451 2345 6789 012', '2015-02-27 12:23:55');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=98 ;

--
-- Dump dei dati per la tabella `Richieste`
--

INSERT INTO `Richieste` (`id`, `id_utente`, `id_fornitore`, `esito`, `tipo_richiesta`, `minuti_risposta`, `distanza`, `data_ora`) VALUES
(93, 1, 130, 'scaduta', 'consulenza', 20, 2.0, '2015-02-26 16:03:24'),
(94, 1, 128, 'scaduta', 'consulenza', 20, 29.2, '2015-02-26 18:09:03'),
(95, 1, 130, 'accettata', 'consulenza', 20, 1.5, '2015-02-27 11:55:09'),
(96, 1, 128, 'accettata', 'consulenza', 20, 19.3, '2015-02-27 12:01:56'),
(97, 1, 130, 'rifiutata', 'preventivo', 15, 9.5, '2015-02-27 13:22:30');

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
  `contatti_mensili` int(11) DEFAULT NULL,
  `rating` float(11,1) DEFAULT NULL,
  `num_valutazioni` int(11) NOT NULL,
  `email_pushbullet` text,
  `data_reg` date DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=131 ;

--
-- Dump dei dati per la tabella `Users`
--

INSERT INTO `Users` (`id`, `nome`, `cognome`, `codfiscale`, `indirizzo`, `telefono`, `email`, `password`, `dati_contatto`, `minuti_attesa`, `partita_iva`, `principale`, `id_azienda`, `contatti_mensili`, `rating`, `num_valutazioni`, `email_pushbullet`, `data_reg`) VALUES
(1, 'Alessandro', 'Mercurio', 'mrclsn24g76i', 'crevalcore via s.agata', '3383344566', 'alessandro.mercurio91@gmail.com', '1234', '011', NULL, NULL, 0, NULL, NULL, 5.0, 1, '9ciotto1@gmail.com', '2014-12-16'),
(2, 'admin', 'admin', 'dmn76g88i', 'bologna', '3386677899', 'admin@webapp.it', 'admin', '111', NULL, NULL, 99, NULL, NULL, NULL, 0, '', '2014-11-16'),
(53, 'administrator', 'administrator', 'admin', 'Bologna', '3334455677', 'administrator@gmail.com', '1LcL880y', '000', NULL, NULL, 99, NULL, NULL, NULL, 0, '9ciotto1@gmail.com', '2015-01-14'),
(128, 'Alessandro', 'Mercurio', 'MRCLSN91H24G467I', 'Crevalcore Via Sant'' Agata 231', '3381234567', '9ciotto1@gmail.com', '1234', '111', 20, '12345678903', 1, 14, NULL, NULL, 0, '9ciotto1@gmail.com', '2015-02-26'),
(130, 'Luigi', 'Mercurio', 'MRCLSN91H24G356I', 'Crevalcore Via Candia 19', '3381234567', 'ciotto-ciotto@hotmail.it', '1234', '111', 15, '12345678903', 0, 14, NULL, NULL, 0, '9ciotto1@gmail.com', '2015-02-26');

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
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `Fatture`
--
ALTER TABLE `Fatture`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Feedback`
--
ALTER TABLE `Feedback`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=59;
--
-- AUTO_INCREMENT for table `Impostazioni`
--
ALTER TABLE `Impostazioni`
MODIFY `option_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `Richieste`
--
ALTER TABLE `Richieste`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=98;
--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=131;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
