<?php

namespace Drupal\s3fs_asset_dumper\Asset;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Asset\AssetDumper;

class S3fsAssetDumper extends AssetDumper {

  public function dump($data, $file_extension) {
    // Prefix filename to prevent blocking by firewalls which reject files
    // starting with "ad*".
    $filename = $file_extension . '_' . Crypt::hashBase64($data) . '.' . $file_extension;
    // Create the css/ or js/ path within the files folder.
    $path = 's3://' . $file_extension;
    $uri = $path . '/' . $filename;
    // Create the CSS or JS file.
    file_prepare_directory($path, FILE_CREATE_DIRECTORY);

    // If CSS/JS gzip compression is enabled and the zlib extension is available
    // then create a gzipped version of this file. This file is served
    // conditionally to browsers that accept gzip using .htaccess rules.
    // It's possible that the rewrite rules in .htaccess aren't working on this
    // server, but there's no harm (other than the time spent generating the
    // file) in generating the file anyway. Sites on servers where rewrite rules
    // aren't working can set css.gzip to FALSE in order to skip
    // generating a file that won't be used.
    if (extension_loaded('zlib') && \Drupal::config('system.performance')->get($file_extension . '.gzip')) {
      $data = gzencode($data, 9, FORCE_GZIP);
    }

    if (!file_exists($uri) && !file_unmanaged_save_data($data, $uri, FILE_EXISTS_REPLACE)) {
      return FALSE;
    }
    return $uri;
  }
}
