<?php
declare(strict_types=1);
function db(): PDO { static $pdo; if($pdo instanceof PDO)return $pdo; $c=$GLOBALS['config']['db']; $pdo=new PDO("mysql:host={$c['host']};port={$c['port']};dbname={$c['name']};charset=utf8mb4",$c['user'],$c['pass'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]); $pdo->exec("SET time_zone = '+00:00'"); return $pdo; }
function filas(string $sql,array $p=[]):array{$s=db()->prepare($sql);$s->execute($p);return $s->fetchAll();}
function fila(string $sql,array $p=[]):array|false{$s=db()->prepare($sql);$s->execute($p);return $s->fetch();}
function ejecutar(string $sql,array $p=[]):int{$s=db()->prepare($sql);$s->execute($p);return $s->rowCount();}
