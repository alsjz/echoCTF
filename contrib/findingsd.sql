/* $Id$ */
SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4 COLLATE 'utf8mb4_unicode_ci';

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

DROP TABLE IF EXISTS `findingsd`;
CREATE TABLE `findingsd` (
  srchw CHAR(17) NOT NULL DEFAULT 'ff',
  dsthw CHAR(17) NOT NULL DEFAULT 'ff',
  `size` INT NOT NULL DEFAULT 0,
  proto ENUM('tcp','udp','icmp','tell') default 'tcp',
  srcip BIGINT UNSIGNED default 0,
  dstip BIGINT UNSIGNED default 0,
  dstport INT DEFAULT 0
) ENGINE=BLACKHOLE DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `debuglogs`;
CREATE TABLE debuglogs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  msg longtext
) engine=myisam;

DELIMITER //
DROP TRIGGER IF EXISTS tbi_tcpdump_bh//
CREATE TRIGGER tbi_tcpdump_bh BEFORE INSERT ON findingsd FOR EACH ROW
BEGIN
  DECLARE _PLAYER_ID, _FINDING_ID, _TARGET_ID, _TEAM_ID, CLAIMED_BEFORE, mac_auth, trust_user_ip,teams INT(11) DEFAULT NULL;
  DECLARE userMAC VARCHAR(32) DEFAULT NULL;

  IF (select memc_server_count()<1) THEN
    select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
  END IF;
  SELECT memc_get('sysconfig:debug') INTO @debug;
  SELECT memc_get('sysconfig:mac_auth') INTO mac_auth;
  SELECT memc_get('sysconfig:teams') INTO teams;

  SELECT memc_get(CONCAT('target:',NEW.dstip)) INTO _TARGET_ID;
  IF _TARGET_ID IS NOT NULL THEN
    SELECT memc_get(CONCAT('finding:',NEW.proto,':',ifnull(NEW.dstport,0), ':', _TARGET_ID )) INTO _FINDING_ID;
  END IF;


  IF mac_auth IS NOT NULL AND mac_auth=1 THEN
    SELECT memc_get(CONCAT('arpdat:',NEW.srcip)) INTO userMAC;
    SELECT memc_get(CONCAT('player_mac:',userMAC)) INTO _PLAYER_ID;
  ELSE
    SELECT memc_get(CONCAT('ovpn:',INET_NTOA(NEW.srcip))) INTO _PLAYER_ID;
  END IF;

  IF teams IS NOT NULL AND teams=1 THEN
    SELECT memc_get(CONCAT('team_player:',_PLAYER_ID)) INTO _TEAM_ID;
    SELECT memc_get(CONCAT('team_finding:',_TEAM_ID, ':', _FINDING_ID)) INTO CLAIMED_BEFORE;
  ELSE
    SELECT memc_get(CONCAT('player_finding:',_PLAYER_ID, ':', _FINDING_ID)) INTO CLAIMED_BEFORE;
  END IF;
  IF @debug IS NOT NULL AND @debug=1 THEN
    INSERT DELAYED into debuglogs (msg) VALUES (CONCAT('[BEFORE FINDING] _TARGET_ID:',ifnull(_TARGET_ID,0),' _PLAYER_ID:',ifnull(_PLAYER_ID,0),' _FINDING_ID:',ifnull(_FINDING_ID,0),' _TEAM_ID:',ifnull(_TEAM_ID,'-'),' CLAIMED BEFORE ID:', ifnull(CLAIMED_BEFORE,0)));
  END IF;

  IF _PLAYER_ID IS NOT NULL AND _FINDING_ID IS NOT NULL AND _FINDING_ID>0 THEN
    UPDATE target_ondemand SET heartbeat=NOW() WHERE target_id=_TARGET_ID AND state>0;
  END IF;

  IF _PLAYER_ID IS NOT NULL AND _FINDING_ID IS NOT NULL AND _FINDING_ID>0 AND CLAIMED_BEFORE IS NULL THEN
    INSERT IGNORE INTO player_finding (finding_id,player_id) VALUES(_FINDING_ID,_PLAYER_ID) on duplicate key update finding_id=values(finding_id);
    IF @debug IS NOT NULL AND @debug=1 THEN
      INSERT DELAYED into debuglogs (msg) VALUES (CONCAT('[AFTER FINDING] _TARGET_ID:',ifnull(_TARGET_ID,0),' _PLAYER_ID:',ifnull(_PLAYER_ID,0),' _FINDING_ID:',ifnull(_FINDING_ID,0),' _TEAM_ID:',ifnull(_TEAM_ID,'-'),' CLAIMED BEFORE ID:', ifnull(CLAIMED_BEFORE,0)));
    END IF;
  END IF; /* END count target_mem */
END
//

DROP PROCEDURE IF EXISTS `VPN_LOGIN` //
CREATE PROCEDURE `VPN_LOGIN`(IN usid BIGINT, IN  assignedIP INT UNSIGNED, IN remoteIP INT UNSIGNED)
BEGIN
  IF (select memc_server_count()<1) THEN
    select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
  END IF;

  IF (SELECT COUNT(*) FROM player WHERE id=usid AND status=10)>0 THEN
    SELECT memc_set(CONCAT('ovpn:',usid),INET_NTOA(assignedIP)) into @devnull;
    SELECT memc_set(CONCAT('ovpn:',INET_NTOA(assignedIP)),usid) into @devnull;
    SELECT memc_set(CONCAT('ovpn_remote:',usid),INET_NTOA(remoteIP)) into @devnull;
    UPDATE `player_last` SET `on_vpn`=NOW(), `vpn_local_address`=assignedIP, `vpn_remote_address`=remoteIP WHERE `id`=usid;
  END IF;
END
//

DROP PROCEDURE IF EXISTS `VPN_LOGOUT_STALLED` //
CREATE PROCEDURE `VPN_LOGOUT_STALLED`(IN vpn_server CHAR(15))
BEGIN
  IF (select memc_server_count()<1) THEN
    select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
  END IF;

  SELECT memc_delete(CONCAT('ovpn:',id)), memc_delete(CONCAT('ovpn_remote:',id)) from player_last where vpn_remote_address is not null or vpn_local_address is not null;
  UPDATE player_last SET vpn_remote_address=null, vpn_local_address=null where vpn_remote_address is not null or vpn_local_address is not null;
END
//

DROP PROCEDURE IF EXISTS `VPN_LOGOUT` //
CREATE PROCEDURE `VPN_LOGOUT`(IN usid BIGINT, IN  assignedIP INT UNSIGNED, IN remoteIP INT UNSIGNED)
BEGIN
  IF (select memc_server_count()<1) THEN
    select memc_servers_set('127.0.0.1') INTO @memc_server_set_status;
  END IF;

  IF (SELECT COUNT(*) FROM player WHERE id=usid AND status=10)>0 THEN
    SELECT memc_delete(CONCAT('ovpn:',usid)) into @devnull;
    SELECT memc_delete(CONCAT('ovpn_remote:',usid)) into @devnull;
    SELECT memc_delete(CONCAT('ovpn:',INET_NTOA(assignedIP))) into @devnull;
    UPDATE `player_last` SET vpn_local_address=NULL, vpn_remote_address=NULL WHERE `id`=usid;
  END IF;
END
//
