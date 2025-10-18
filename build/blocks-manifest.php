<?php
// This file is generated. Do not modify it manually.
return array(
	'dynamic-block' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'my-plugin-boilerplate/dynamic-block',
		'version' => '1.0.0',
		'title' => 'Dynamic Block Example',
		'category' => 'widgets',
		'icon' => 'media-interactive',
		'description' => 'An interactive block with the Interactivity API.',
		'example' => array(
			
		),
		'supports' => array(
			'interactivity' => true
		),
		'textdomain' => 'my-plugin-boilerplate',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php',
		'viewScriptModule' => 'file:./view.js'
	),
	'static-block' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'my-plugin-boilerplate/static-block',
		'version' => '1.0.0',
		'title' => 'Static Block Example',
		'category' => 'widgets',
		'icon' => 'list-view',
		'description' => 'Example block scaffolded.',
		'example' => array(
			
		),
		'supports' => array(
			'html' => false
		),
		'textdomain' => 'my-plugin-boilerplate',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'viewScript' => 'file:./view.js'
	)
);
