<?php

namespace App\Helpers;

/**
 * Gravatar Helper
 */
class GravatarHelper
{
    /**
     * Get Gravatar URL for a specified email.
     *
     * @param string $email Email address
     * @param int $s Size of the Gravatar image
     * @param string $d Default image to use if no Gravatar is found
     * @param string $r Maximum rating (g, pg, r, x)
     * @param bool $img Whether to return an image tag
     * @param array $atts Additional attributes for the image tag
     * @return string URL of the Gravatar or a default image
     */
    public static function getGravatar(string $email, int $s = 40, string $d = 'mm', string $r = 'g', bool $img = false, array $atts = []): string
    {
        $url = '//www.gravatar.com/avatar/' . md5(strtolower(trim($email))) . "?s=$s&d=$d&r=$r";

        if (!$url) {
            return base_url() . "files/media/no-pic.png";
        }

        return $url;
    }

    /**
     * Get user picture or Gravatar based on the profile picture setting.
     *
     * @param string|null $pic Profile picture filename
     * @param string|null $email User's email address
     * @param string|null $pixel Optional pixel version of the picture
     * @return string URL of the user picture or Gravatar
     */
    public static function getUserPic(?string $pic = null, ?string $email = null, ?string $pixel = null): string
    {
        if ($pic !== 'no-pic.png') {
            $image = base_url() . "files/media/" . $pic;

            if ($pixel) {
                $picInPixel = base_url() . "files/media/" . $pixel . "_" . $pic;
                // Check if the pixel version exists; if not, fallback to default
                if (!file_exists($picInPixel)) {
                    return $image; // Or return a default image if needed
                }
                return $picInPixel;
            } else {
                return $image;
            }
        } else {
            return self::getGravatar($email);
        }
    }
}
