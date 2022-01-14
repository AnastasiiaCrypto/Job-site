<?php
/**
 * JobClass - Geolocalized Job Board Script
 * Copyright (c) BedigitCom. All Rights Reserved
 *
 * Website: http://www.bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from Codecanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
 */

namespace Larapen\TextToImage;

use Larapen\TextToImage\Libraries\Settings;
use Larapen\TextToImage\Libraries\TextToImageEngine;

class TextToImage
{
    /**
     * @param       $string
     * @param       $format
     *
     * @param array $overrides
     * @param bool $encoded
     *
     * @return string
     */
    public function make($string, $format = IMAGETYPE_JPEG, $overrides = array(), $encoded = true)
    {
        if (trim($string) == '') {
            return $string;
        }

        $settings = Settings::createFromIni(__DIR__ . DIRECTORY_SEPARATOR . 'settings.ini');
        $settings->format = $format;
        $settings->fontFamily = __DIR__ . '/Libraries/' . $settings->fontFamily;
        $settings->assignProperties($overrides);
        
        $image = new TextToImageEngine($settings);
        $image->setText($string);
        
        if ($encoded) {
            return $image->getEmbeddedImage();
        }
        
        return $image;
    }
}
