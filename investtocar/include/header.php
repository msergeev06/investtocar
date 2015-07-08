<?php
	/**
	 * Основные установки
	 */
	define("INVESTTOCAR_PATH",$_SERVER["DOCUMENT_ROOT"]."/msergeev/investtocar/");
	define("INVESTTOCAR_INCLUDE_PATH", INVESTTOCAR_PATH."include/");
	define("INVESTTOCAR_CLASS_PATH",INVESTTOCAR_INCLUDE_PATH."class/");
	require_once($_SERVER["DOCUMENT_ROOT"]."/config.php");
	require_once (INVESTTOCAR_INCLUDE_PATH."config.php");

	/**
	 * Подключаем переводы
	 */
	require_once(INVESTTOCAR_INCLUDE_PATH."lang/ru/allmessage.php");
	require_once(INVESTTOCAR_INCLUDE_PATH."getmessage.php");

	/**
	 * Подключаем базу данных
	 */
	require_once (INVESTTOCAR_CLASS_PATH."database.php");
	$DB = new CInvestToCarDB (DB_HOST, 80, DB_USER, DB_PASSWORD, DB_NAME);

	/**
	 * Подключаем классы
	 */
	//Класс опций
	require_once (INVESTTOCAR_CLASS_PATH."options.php");
	$OPTIONS = new CInvestToCarOptions();
	//Основной класс
	require_once (INVESTTOCAR_CLASS_PATH."main.php");
