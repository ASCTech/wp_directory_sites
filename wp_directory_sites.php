<?php
/*
Plugin Name: WP Directory Sites
Version: .1
Description: Allows for the creation of sites that are just directories
Author: John Colvin
*/

ini_set('display_errors', true);
error_reporting(E_ALL);

class DirectorySitePlugin {
  
  function DirectorySitePlugin() {
    add_action('admin_menu', array(&$this, 'add_options_page'));
  }
  
  function add_options_page() {
    add_submenu_page('index.php', 'New Directory Site', 'New Directory Site', 'manage_sites', 'new-directory-site', array(&$this, 'site_create_new'));
  }
  
  function site_create_new() {
    if (!empty($_POST)) {
      try {
        $username = $this->verifyUsername($_POST['username']);
        $this->createUserDirectory($username);
        $successMessage = 'Directory site successfully created for ' . $username;
      }
      catch (Exception $e) {
        $errorMessage = $e->getMessage();
      }
    }
    
  if (isset($successMessage)) {
    echo '<p>' . $successMessage . '</p>';
  }
  if (isset($errorMessage)) {
    echo '<p>' . $errorMessage . '</p>';
  }  
  
  ?>
  <p>Create a new directory site for (name.#):</p>
  <form method=POST>
    <input type="text" name="username" />
    <input type="submit" value="Submit" />
  </form>
  <?php

  }
  
  
  function verifyUsername($username) {
    $matchCount = preg_match('/\A[a-z]([a-z-]*[a-z])?\.[1-9]\d*\z/', $username);
    if (empty($matchCount)) {
      throw new Exception('Invalid name.n. Please try again.');
    }
    
    return $username;
  }
  
  function createUserDirectory($username) {
    $userDir = $this->getUserDirectory($username);  
    
    if ($this->userDirectoryExists($username)) {
      throw new Exception('A directory for this user already exists');
    }
    
    $ftpUser = 'ftp';
    $ftpGroup = 'ftp';
    $mode = 0775;
    
    if (!mkdir($userDir, $mode)) {
      throw new Exception('Could not make user directory');
    }
    
    chown($userDir, $ftpUser);
    chgrp($userDir, $ftpGroup); 
  }
  
  function userDirectoryExists($username) {
    $userDir = $this->getUserDirectory($username);
    return file_exists($userDir); 
  }
  
  function getUserDirectory($username) {
    return '/home/vftp/' . $username;
  }
}

$directorySitePlugin = new DirectorySitePlugin();
