<?php

use ZipArchive;

function unzip($src_file, $dest_dir = false, $create_zip_name_dir = true, $overwrite = true)
{
  $zip = new ZipArchive();

  // Try to open the zip file
  if ($zip->open($src_file) === TRUE) {
    $splitter = ($create_zip_name_dir === true) ? "." : "/";

    // If the destination directory is not set, use the source file's directory
    if ($dest_dir === false) {
      $dest_dir = substr($src_file, 0, strrpos($src_file, $splitter)) . "/";
    }

    // Create destination directories if they do not exist
    create_dirs($dest_dir);

    // Iterate over each file in the zip archive
    for ($i = 0; $i < $zip->numFiles; $i++) {
      $zip_entry = $zip->getNameIndex($i);
      $pos_last_slash = strrpos($zip_entry, "/");

      // Create directory for the file if it’s in a subdirectory
      if ($pos_last_slash !== false) {
        create_dirs($dest_dir . substr($zip_entry, 0, $pos_last_slash + 1));
      }

      $file_name = $dest_dir . $zip_entry;

      // Check if file should be overwritten or skipped
      if ($overwrite === true || ($overwrite === false && !is_file($file_name))) {
        // Check if the entry is a directory or a file
        if (!is_dir($file_name)) {
          // Extract the file from the zip archive
          file_put_contents($file_name, $zip->getFromIndex($i));
        }
      }
    }

    // Close the zip file after extraction
    $zip->close();
    return true;
  } else {
    return false; // Failed to open the zip file
  }
}

/**
 * This function creates recursive directories if they don't already exist
 *
 * @param String $path The path that should be created
 *
 * @return void
 */
function create_dirs($path)
{
  if (!is_dir($path)) {
    // Create directory recursively
    mkdir($path, 0755, true);
  }
}
